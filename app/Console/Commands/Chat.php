<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\Themes\Default\Concerns\DrawsBoxes;
use Prism\Prism\Enums\FinishReason;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Tool;
use Prism\Prism\Prism;
use Prism\Prism\Tool as PrismTool;
use Prism\Prism\ValueObjects\Messages\UserMessage;

use function Laravel\Prompts\spin;
use function Laravel\Prompts\textarea;

class Chat extends Command
{
    use Colors, DrawsBoxes;

    protected $signature = 'chat';

    protected $description = 'Customer support chat';

    public function handle()
    {
        $messages = collect();

        $input = textarea('Message', required: true);
        $messages->push(new UserMessage($input));

        while (true) {
            $prism = Prism::text()
                ->using(Provider::Anthropic, 'claude-3-7-sonnet-latest')
                ->withSystemPrompt('You are a customer support agent for Echo Labs')
                ->withTools($this->tools())
                ->withMessages($messages->toArray());

            $response = spin(fn () => $prism->asText());

            $messages = $response->messages;

            $this->box(
                'Assistant',
                wordwrap($response->text, 60)
            );

            foreach ($response->toolCalls as $toolCall) {
                $this->box(
                    'Tool Call',
                    $toolCall->name,
                    color: 'cyan',
                    footer: json_encode($toolCall->arguments())
                );
            }

            if ($response->finishReason === FinishReason::Stop) {
                $input = textarea('Message', required: true);
                $messages->push(new UserMessage($input));
            }
        }
    }

    protected function tools(): array
    {
        return [
            $this->orderTool(),
        ];
    }

    protected function orderTool(): PrismTool
    {
        return Tool::as('customer_order_tool')
            ->for('getting information about customer orders')
            ->withStringParameter(
                'orderId',
                'The order ID',
                true
            )
            ->using(function (string $orderId) {
                return Order::with('items')->find($orderId)->toJson();
            });
    }
}
