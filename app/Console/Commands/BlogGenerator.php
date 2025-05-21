<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

use function Laravel\Prompts\select;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\text;

class BlogGenerator extends Command
{
    use Colors, DrawsBoxes;

    protected $signature = 'blog-generator';

    public function handle()
    {
        $provider = Provider::from(select('Provider', $this->providers()));
        $model = $this->resolveModel($provider);
        $topic = text('Topic');
        $tone = select('Tone', $this->tones());

        $prism = Prism::text()
            ->using($provider, $model)
            ->withSystemPrompt(view('prompts.blog-generator', [
                'tone' => $tone,
            ]))
            ->withPrompt("Write a blog post on {$topic}");

        // $response = spin(fn () => $prism->asText(), 'generating...');
        //
        // $this->box(
        //     'Response',
        //     wordwrap($response->text, 60)
        // );

        $response = $prism->asStream();

        foreach ($response as $chunk) {
            echo $chunk->text;
        }
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
            Provider::Anthropic => 'claude-sonnet-3-7-latest',
            Provider::Gemini => 'gemini-2.0-flash',
        };
    }

    protected function tones(): array
    {
        return [
            'Humerous',
            'Professional',
            'Informative',
            'Casual',
            'Gen Z',
        ];
    }
}
