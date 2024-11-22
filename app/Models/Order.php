<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'event_date',
        'ticket_adult_price',
        'ticket_adult_quantity',
        'ticket_kid_price',
        'ticket_kid_quantity',
        'barcode',
        'user_id',
        'total_price',
    ];

    public static function createOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $user_id)
    {
        $client = new Client();

        // Генерация уникального штрих-кода
        $barcode = Str::random(10);

        // Рассчитываем общую цену
        $total_price = ($ticket_adult_price * $ticket_adult_quantity) + ($ticket_kid_price * $ticket_kid_quantity);

        // Данные для отправки в API
        $data = [
            'event_id' => $event_id,
            'event_date' => $event_date,
            'ticket_adult_price' => $ticket_adult_price,
            'ticket_adult_quantity' => $ticket_adult_quantity,
            'ticket_kid_price' => $ticket_kid_price,
            'ticket_kid_quantity' => $ticket_kid_quantity,
            'barcode' => $barcode
        ];

        // Попытка брони заказа
        try {
            $response = $client->post('https://api.site.com/book', [
                'json' => $data
            ]);

            $responseBody = json_decode($response->getBody(), true);

            // Проверка ответа от API
            if (isset($responseBody['message']) && $responseBody['message'] == 'заказ успешно забронирован') {
                // Подтверждение заказа
                $approveResponse = $client->post('https://api.site.com/approve', [
                    'json' => ['barcode' => $barcode]
                ]);

                $approveResponseBody = json_decode($approveResponse->getBody(), true);

                if (isset($approveResponseBody['message']) && $approveResponseBody['message'] == 'заказ успешно одобрен') {
                    // Сохранение заказа в базе данных
                    return self::create([
                        'event_id' => $event_id,
                        'event_date' => $event_date,
                        'ticket_adult_price' => $ticket_adult_price,
                        'ticket_adult_quantity' => $ticket_adult_quantity,
                        'ticket_kid_price' => $ticket_kid_price,
                        'ticket_kid_quantity' => $ticket_kid_quantity,
                        'barcode' => $barcode,
                        'user_id' => $user_id,
                        'total_price' => $total_price,
                    ]);
                } else {
                    throw new \Exception('Ошибка подтверждения заказа: ' . ($approveResponseBody['error'] ?? 'Неизвестная ошибка'));
                }
            } else {
                throw new \Exception('Ошибка бронирования заказа: ' . ($responseBody['error'] ?? 'Неизвестная ошибка'));
            }
        } catch (\Exception $e) {
            // Логирование ошибки или другая обработка ошибки
            throw $e;
        }
    }
}
