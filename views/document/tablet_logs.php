<?php
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $token string */

// Регистрируем стили для красивого отображения кода
$this->registerCss("
    pre.json-pre { background: #1e1e1e; color: #7ec699; padding: 15px; border-radius: 8px; font-size: 13px; max-height: 500px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
    .table-success-custom { background-color: #e8f5e9 !important; }
    .view-json-btn { min-width: 180px; }
");
?>

<div class="payment-logs-index">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Журнал поллинга СБП (Планшет)</h2>
        <div>
            <!-- КНОПКА СБРОСА ФИЛЬТРОВ И ПЕРЕЗАГРУЗКИ -->
            <?= Html::a('🔄 Сбросить фильтры', ['logs', 'token' => $token], [
                    'class' => 'btn btn-outline-secondary me-2',
                    'title' => 'Очистить все параметры поиска и вернуть таблицу к исходному виду'
            ]) ?>
            <span class="badge bg-dark">Режим отладки</span>
        </div>
    </div>

    <!-- ТЕХНИЧЕСКАЯ ДОКУМЕНТАЦИЯ РАЗДЕЛА -->
    <div class="doc-block">
        <details>
            <summary class="fw-bold text-primary" style="cursor: pointer; user-select: none;">
                📖 Справка разработчика: Как читать эти логи и искать задержки СБП?
            </summary>
            <div class="mt-3 small text-muted" style="line-height: 1.6;">
                <p><strong>1. Что это за журнал?</strong><br>
                    Сюда записывается каждый «тик» поллинга Alpine.js с планшетов (запросы идут каждые 5-10 секунд). Логи пишутся в таблицу <code>alfa_payment_logs</code> методом <code>InfoLog::addPaymentLog()</code> из <code>ApiController::actionCheckPaymentStatus()</code>. Таблица автоматически очищается от записей старше 3 дней.</p>

                <p><strong>2. Как выявить причину задержки скрытия QR-кода (30 секунд)?</strong><br>
                    Вбейте в фильтр <strong>ID Визита</strong> целевого пациента. Строки выстроятся по секундам (от ранних к поздним).
                <ul>
                    <li><span class="text-dark fw-bold">Шаг времени (отсчет в скобках):</span> Показывает реальный интервал между запросами планшета. Если между строками разрыв в 20-30 секунд — у планшета в клинике <u>отваливался интернет</u>.</li>
                    <li><span class="text-dark fw-bold">Изучение JSON-слепка:</span> Раскройте лог операции. Если в течение 30 секунд в массиве <code>СПИСОК_СЧЕТОВ_ИЗ_МИС_ПО_ПАЦИЕНТУ</code> у целевого визита висит старый номер счета и <code>status_code = 0</code> — значит, <u>задерживает банк или сама МИС</u> не обновляет статус в БД. Планшет работает штатно, он просто ждет отмашки от МИС.</li>
                </ul>
                </p>

                <p><strong>3. Что означают статусы?</strong><br>
                <ul>
                    <li><code>⏳ Ожидание (0)</code> — МИС сообщает, что счет еще не оплачен. Планшет продолжает крутить QR-код.</li>
                    <li><code>✅ Оплачено (2)</code> — МИС подтвердила оплату. Бэкенд выслал на планшет команду <code>is_payed = 2</code>. Строка подсвечивается <span class="badge bg-success">зеленым</span>. На следующем тике экран QR закроется.</li>
                    <li><code>Локальный Payment: success</code> — наша внутренняя таблица успешно сохранила новый <code>invoice_number_real</code> от СБП.</li>
                </ul>
                </p>
            </div>
        </details>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <?php Pjax::begin(['id' => 'logs-pjax']); ?>

            <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'tableOptions' => ['class' => 'table table-bordered table-striped table-hover align-middle'],
                    'rowOptions' => function ($model) {
                        $data = json_decode($model->response_data, true) ?: [];
                        if (isset($data['found_status']) && (int)$data['found_status'] === 2) {
                            return ['class' => 'table-success-custom']; // Зеленая строка при успешной оплате
                        }
                        return [];
                    },
                    'columns' => [
                            [
                                    'attribute' => 'created_at',
                                    'label' => 'Время тика',
                                    'value' => function($model) {
                                        return date('H:i:s d.m.Y', strtotime($model->created_at));
                                    },
                                    'options' => ['style' => 'width: 160px;'],
                            ],
                            [
                                    'attribute' => 'appointment_id',
                                    'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                    'attribute' => 'patient_id',
                                    'options' => ['style' => 'width: 120px;'],
                            ],
                            [
                                    'attribute' => 'invoice_number',
                                    'options' => ['style' => 'width: 140px;'],
                            ],
                            [
                                    'label' => 'Статус',
                                    'contentOptions' => ['class' => 'text-center'],
                                    'options' => ['style' => 'width: 150px;'],
                                    'value' => function($model) {
                                        $data = json_decode($model->response_data, true) ?: [];
                                        $status = $data['found_status'] ?? 0;
                                        return $status == 2 ? '✅ Оплачено (2)' : '⏳ Ожидание (' . $status . ')';
                                    }
                            ],
                            [
                                    'label' => 'Локальный Payment',
                                    'options' => ['style' => 'width: 160px;'],
                                    'value' => function($model) {
                                        $data = json_decode($model->response_data, true) ?: [];
                                        return $data['local_payment_save_status'] ?? '—';
                                    }
                            ],
                            [
                                    'label' => 'Данные ответа МИС',
                                    'format' => 'raw',
                                    'value' => function($model) {
                                        $rawPayload = json_decode($model->response_data, true) ?: [];

                                        $cleanStructure = [
                                                'СТАТУС_ДЛЯ_ФРОНТЕНДА' => (isset($rawPayload['found_status']) && $rawPayload['found_status'] == 2)
                                                        ? '✅ ОПЛАЧЕНО (Команда на скрытие QR отправлена)'
                                                        : '⏳ ОЖИДАНИЕ_ОПЛАТЫ',

                                                'ЛОКАЛЬНАЯ_БАЗА_ДАННЫХ' => [
                                                        'Статус_сохранения_модели' => $rawPayload['local_payment_save_status'] ?? '—',
                                                        'Атрибуты_записи_таблицы' => $rawPayload['local_payment_attributes'] ?? '—'
                                                ],

                                                'СПИСОК_СЧЕТОВ_ИЗ_МИС_ПО_ПАЦИЕНТУ' => []
                                        ];

                                        // Извлекаем инвойсы (проверяем возможную двойную вложенность 'allInvoices' => ['allInvoices' => ...])
                                        $invoices = $rawPayload['allInvoices'] ?? [];
                                        if (isset($invoices['allInvoices'])) {
                                            $invoices = $invoices['allInvoices'];
                                        }

                                        if (!empty($invoices) && is_array($invoices)) {
                                            foreach ($invoices as $idx => $inv) {
                                                // Если МИС вернула кастомную структуру обертки, заглядываем внутрь
                                                $item = $inv['allInvoices'] ?? $inv;

                                                $appId = $item['appointment_id'] ?? null;
                                                $isTarget = ($appId && (int)$appId === (int)$model->appointment_id);

                                                $cleanStructure['СПИСОК_СЧЕТОВ_ИЗ_МИС_ПО_ПАЦИЕНТУ']["Счет_# " . ((int)$idx + 1) . ($isTarget ? ' [ЦЕЛЕВОЙ_ВИЗИТ]' : '')] = [
                                                        'number' => $item['number'] ?? $item['invoice_number'] ?? '—',
                                                        'appointment_id' => $appId ?? '—',
                                                        'status_code' => $item['status_code'] ?? '—',
                                                        'status_title' => $item['status_title'] ?? $item['status'] ?? '—',
                                                        'sum' => isset($item['sum']) ? $item['sum'] . ' руб.' : (isset($item['amount']) ? $item['amount'] . ' руб.' : '—'),
                                                        'date' => $item['date'] ?? $item['created_at'] ?? '—',
                                                ];
                                            }
                                        } else {
                                            $cleanStructure['СПИСОК_СЧЕТОВ_ИЗ_МИС_ПО_ПАЦИЕНТУ'] = '❌ МИС вернула ПУСТОЙ массив счетов (getInvoices => [])';
                                        }

                                        // Кодируем БЕЗ экранирования кавычек для тега script
                                        $cleanJson = json_encode($cleanStructure, JSON_UNESCAPED_UNICODE);

                                        $btnId = 'btn-json-' . $model->id;
                                        $containerId = 'container-json-' . $model->id;
                                        $scriptId = 'raw-json-' . $model->id;
                                        $preId = 'pre-json-' . $model->id;

                                        $html = Html::button('Показать логи операции', [
                                                'class' => 'btn btn-sm btn-outline-primary view-json-btn',
                                                'id' => $btnId,
                                                'data-log-id' => $model->id,
                                        ]);

                                        // Вставляем чистый JSON внутрь скрипта. Текст внутри скрипта безопасен для рендеринга.
                                        $html .= '<script type="text/json" id="' . $scriptId . '">' . $cleanJson . '</script>';
                                        $html .= '<div class="mt-2 d-none" id="' . $containerId . '"><pre id="' . $preId . '" class="json-pre"></pre></div>';

                                        return $html;
                                    }
                            ],
                    ],
            ]); ?>

            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

<script>
    function initJsonViewers() {
        // Защита от дублирования слушателей внутри Pjax
        document.querySelectorAll('.view-json-btn').forEach(button => {
            button.replaceWith(button.cloneNode(true));
        });

        document.querySelectorAll('.view-json-btn').forEach(button => {
            button.addEventListener('click', function() {
                const logId = this.getAttribute('data-log-id');
                const container = document.getElementById(`container-json-${logId}`);
                const pre = document.getElementById(`pre-json-${logId}`);

                if (container.classList.contains('d-none')) {
                    const rawJsonText = document.getElementById(`raw-json-${logId}`).textContent;
                    try {
                        const parsedObj = JSON.parse(rawJsonText);
                        pre.textContent = JSON.stringify(parsedObj, null, 4);
                    } catch (e) {
                        pre.textContent = rawJsonText;
                    }
                    container.classList.remove('d-none');
                    this.textContent = 'Скрыть логи';
                    this.classList.replace('btn-outline-primary', 'btn-secondary');
                } else {
                    container.classList.add('d-none');
                    this.textContent = 'Показать логи операции';
                    this.classList.replace('btn-secondary', 'btn-outline-primary');
                }
            });
        });
    }

    // Запуск обработчика
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initJsonViewers);
    } else {
        initJsonViewers();
    }
    document.addEventListener('pjax:success', initJsonViewers);
</script>
