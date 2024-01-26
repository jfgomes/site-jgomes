<?php

namespace App\Http\Controllers;

use App\Jobs\MessagesJob;
use App\Models\Messages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{
    private $messagesModel;

    public function __construct(Messages $messagesModel)
    {
        $this->messagesModel = $messagesModel;
    }

    /**
     * @OA\Post(
     *     path="/api/send",
     *     summary="Send a message",
     *     tags={"Message"},
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="User's name",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="email",
     *         in="query",
     *         description="User's email",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="subject",
     *         in="query",
     *         description="User's subject",
     *         required=false,
     *         @OA\Schema(type="string")
            ),
     *      @OA\Parameter(
     *          name="content",
     *          in="query",
     *          description="User's content",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="201", description="Message sent successfully"),
     *     @OA\Response(response="422", description="Validation errors")
     * )
     */
    public function send(Request $request): JsonResponse
    {
        try {

            // Validate received data
            $this->validateData($request->all());

            // Prepare message
            $message = $this->prepareMessage($request);

            // Send message to the RabbitMQ queue
            $queue = env('RABBIT_MESSAGE_QUEUE');
            dispatch(new MessagesJob(json_encode($message), $queue));

        } catch (\Exception $e) {
            return response()->json(['error']);
        }

        // If flow reaches here, everything worked fine!
        // Confirm if it is an API
        $isApiRequest = $request->is('api/*');
        if ($isApiRequest) {
            return response()->json(['success-api']);
        }

        return response()->json(['success-site']);
    }

    /**
     * Validate the received data using the Messages model.
     *
     * @param array $data
     * @return void
     */
    private function validateData(array $data): void
    {
        $validator = $this->messagesModel->validateData($data);
        if ($validator->fails()) {
            // Log validation errors and return a 422 Unprocessable Entity response
            $errors = $validator->errors()->toArray();
            $this->logError('Validation failed: ' . json_encode($errors));
            abort(response()->json(['error' => 'Validation failed'], 422));
        }
    }

    /**
     * Prepare the message data from the request.
     *
     * @param Request $request
     * @return array
     */
    private function prepareMessage(Request $request): array
    {
        return [
            'name'    => $request->input('name'),
            'email'   => $request->input('email'),
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
        ];
    }

    /**
     * Log an error message to the 'messages' channel.
     *
     * @param string $message
     * @return void
     */
    private function logError(string $message): void
    {
        Log::channel('messages')
            ->error('Error on Controller receiving message from client: ' . $message);
    }
}
