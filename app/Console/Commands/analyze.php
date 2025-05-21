<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\EnumSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;
use Prism\Prism\ValueObjects\Messages\Support\Document;
use Prism\Prism\ValueObjects\Messages\Support\Image;
use Prism\Prism\ValueObjects\Messages\UserMessage;

use function Laravel\Prompts\select;

class analyze extends Command
{
    use Colors, DrawsBoxes;

    protected $signature = 'analyze {file}';

    public function handle()
    {
        $provider = Provider::from(select('Provider', $this->providers()));
        $model = $this->resolveModel($provider);

        $file = storage_path($this->argument('file'));

        $type = select('Analysis Type', [
            'summary',
            'key_points',
            'sentiment',
        ]);

        $document = Str::startsWith(mime_content_type($file), 'image/')
            ? Image::fromPath($file)
            : Document::fromPath($file);

        $prism = Prism::structured()
            ->using($provider, $model)
            ->withSystemPrompt(view('prompts.analyze', [
                'type' => $type,
            ]))
            ->withSchema($this->schemas($type))
            ->withMessages([
                new UserMessage(
                    'Analyze the image and give me a report',
                    [
                        $document,
                    ]
                ),
            ]);

        $response = $prism->asStructured();

        dd($response->structured);
    }

    protected function providers(): array
    {
        return [
            Provider::OpenAI->value,
            Provider::Gemini->value,
            Provider::Anthropic->value,
        ];
    }

    protected function resolveModel(Provider $provider): string
    {
        return match ($provider) {
            Provider::OpenAI => 'gpt-4.1',
            Provider::Anthropic => 'claude-3-7-sonnet-latest',
            Provider::Gemini => 'gemini-2.0-flash',
        };
    }

    protected function schemas(string $type): ObjectSchema
    {
        return match ($type) {
            'summary' => new ObjectSchema(
                'summary',
                'A detailed summary of the documents contents',
                [
                    new StringSchema(
                        'title',
                        'A title for the document summary'
                    ),
                    new StringSchema(
                        'summary',
                        'the detailed document summary'
                    ),
                    new StringSchema(
                        'main_topic',
                        'The main topic of the document'
                    ),
                    new ArraySchema(
                        'key_points',
                        'the key points of the document',
                        new StringSchema('key_point', 'a key point of the doument')
                    ),
                ],
                ['title', 'summary', 'main_topic', 'key_points']
            ),
            'key_points' => new ObjectSchema(
                'key_points',
                'Key points from the document content',
                [
                    new StringSchema('document_type', 'The document type'),
                    new ArraySchema(
                        'points',
                        'key points of the document',
                        new ObjectSchema(
                            'point',
                            'A key point from the documents key points',
                            [
                                new StringSchema('title', 'a title for the key point'),
                                new StringSchema('content', 'the actual key point'),
                            ],
                            ['title', 'content']
                        )
                    ),
                ],
                ['points']
            ),
            'sentiment' => new ObjectSchema(
                'sentiment',
                'a sentiment analysis of the documents contents',
                [
                    new EnumSchema(
                        'overall_sentiment',
                        'The overall sentiment of the document',
                        [
                            'very_negative',
                            'negative',
                            'netural',
                            'positive',
                            'very_positive',
                        ]
                    ),
                    new StringSchema(
                        'sentiment_explanation',
                        'an explanation of the sentiment analysis'
                    ),
                    new ArraySchema(
                        'emotional_tokens',
                        'The emotional tokens and tone of the sentiment analysis',
                        new ObjectSchema(
                            'tone',
                            'the tones in the documents analysis',
                            [
                                new StringSchema(
                                    'name',
                                    'the name of the tone',
                                ),
                                new StringSchema(
                                    'evidence',
                                    'evidence of the tone'
                                ),
                            ],
                            [
                                'name',
                                'evidence',
                            ]
                        )
                    ),
                ],
                [
                    'overall_sentiment',
                    'sentiment_explanation',
                    'emotional_tokens',
                ]
            )
        };
    }
}
