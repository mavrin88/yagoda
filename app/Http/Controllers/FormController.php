<?php

    namespace App\Http\Controllers;

    use App\Http\Requests\Forms\FormFrontConnectRequest;
    use App\Http\Requests\Forms\FormFrontFooterRequest;
    use App\Http\Requests\Forms\FormFrontModalRequest;
    use App\Jobs\SendEmailJob;
    use App\Mail\SendEmail;
    use App\Models\Form;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Http\Request;

    class FormController extends Controller
    {

        /**
         * Пока не используется
         *
         * @param \App\Http\Requests\Forms\FormFrontConnectRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function frontConnect(FormFrontConnectRequest $request): \Illuminate\Http\JsonResponse {
            $data = $request->safe()->only('firm', 'inn', 'name', 'phone', 'city', 'crm');

            $subject = 'Форма Подать заявку на подключение на главной странице';
            $emailToArray = [config('mail.to.address'), config('mail.to2.address')];

            if (!empty($data['firm']) && !empty($data['inn']) && !empty($data['name']) && !empty($data['phone']) && !empty($data['city'])) {
                try {
                    SendEmailJob::dispatch($emailToArray, new SendEmail($data, $subject, config('mail.from.address'), config('mail.to.address')));

                    $form = new Form();
                    $form->type = 'front-connect';
                    $form->payload = json_encode($data);
                    $form->save();

                    return response()->json([
                        'success' => TRUE,
                        'message' => 'Форма успешно отправлена',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());

                    return response()->json([
                        'success' => FALSE,
                        'message' => 'Форма не отправлена',
                    ]);
                }
            }
            else {
                return response()->json([
                    'success' => FALSE,
                    'message' => 'Форма не отправлена',
                ]);
            }
        }

        /**
         * Пока не используется
         *
         * @param \App\Http\Requests\Forms\FormFrontModalRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function frontModal(FormFrontModalRequest $request): \Illuminate\Http\JsonResponse {
            $data = $request->safe()->only('name', 'phone');

            $subject = 'Форма Подать заявку на подключение на главной странице в попапе';
            $emailToArray = [config('mail.to2.address'), config('mail.to3.address')];

            if (!empty($data['phone'])) {
                try {
                    foreach ($emailToArray as $email) {
                        SendEmailJob::dispatch($email, new SendEmail($data, $subject, config('mail.from.address')));
                    }

                    $form = new Form();
                    $form->type = 'front-modal';
                    $form->payload = json_encode($data);
                    $form->save();

                    return response()->json([
                        'success' => TRUE,
                        'message' => 'Форма успешно отправлена',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());

                    return response()->json([
                        'success' => FALSE,
                        'message' => 'Форма не отправлена',
                    ]);
                }
            }
            else {
                return response()->json([
                    'success' => FALSE,
                    'message' => 'Форма не отправлена',
                ]);
            }
        }


        /**
         * Подвал лендингов
         *
         * @param \App\Http\Requests\Forms\FormFrontFooterRequest $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function frontFooter(FormFrontFooterRequest $request): \Illuminate\Http\JsonResponse {
            $data = $request->safe()->only('phone');

            $subject = 'Форма Подать заявку на подключение из подвала лендингов';
            $emailToArray = [config('mail.to2.address'), config('mail.to3.address')];

            if (!empty($data['phone'])) {
                try {
                    foreach ($emailToArray as $email) {
                        SendEmailJob::dispatch($email, new SendEmail($data, $subject, config('mail.from.address')));
                    }

                    $form = new Form();
                    $form->type = 'front-footer';
                    $form->payload = json_encode($data);
                    $form->save();

                    return response()->json([
                        'success' => TRUE,
                        'message' => 'Форма успешно отправлена',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());

                    return response()->json([
                        'success' => FALSE,
                        'message' => 'Форма не отправлена',
                    ]);
                }
            }
            else {
                return response()->json([
                    'success' => FALSE,
                    'message' => 'Форма не отправлена',
                ]);
            }
        }


        /**
         * Заказ стендов из tips
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function orderStand(Request $request): \Illuminate\Http\JsonResponse {
            $validated = $request->validate([
                'type' => 'required|string',
            ]);

            $subject = 'Форма Заказ стоек для чаевых';
            $emailToArray = [config('mail.to2.address'), config('mail.to3.address')];

            if (!empty($validated['type'])) {
                try {
                    foreach ($emailToArray as $email) {
                        SendEmailJob::dispatch($email, new SendEmail($validated, $subject, config('mail.from.address')));
                    }

                    $form = new Form();
                    $form->type = 'tips-stands';
                    $form->payload = json_encode($validated);
                    $form->save();

                    return response()->json([
                        'success' => TRUE,
                        'message' => 'Форма успешно отправлена',
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error sending email: ' . $e->getMessage());

                    return response()->json([
                        'success' => FALSE,
                        'message' => 'Форма не отправлена',
                    ]);
                }
            }
            else {
                return response()->json([
                    'success' => FALSE,
                    'message' => 'Форма не отправлена',
                ]);
            }
        }
    }
