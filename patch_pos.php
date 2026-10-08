<?php
$content = file_get_contents('app/Http/Controllers/PosController.php');

$oldValidation = <<<PHP
    public function store(Request \$request)
    {
        \$request->validate([
            'payment_method' => 'required|in:cash,qris',
            'paid_amount'    => 'required|numeric|min:0',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:menus,id',
            'items.*.qty'    => 'required|integer|min:1',
        ]);
PHP;

$newValidation = <<<PHP
    public function store(Request \$request)
    {
        \$request->validate([
            'payment_method' => 'required|in:cash,qris',
            'paid_amount'    => 'required|numeric|min:0',
            'order_type'     => 'nullable|in:dine_in,takeaway,online',
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:menus,id',
            'items.*.qty'    => 'required|integer|min:1',
        ]);
PHP;

$content = str_replace($oldValidation, $newValidation, $content);

$oldCreate = <<<PHP
                \$trx = Transaction::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'user_id'        => auth()->id(),
                    'total_amount'   => \$totalCalculated,
                    'payment_method' => \$request->payment_method,
                    'paid_amount'    => \$paidAmount,
                    'change_amount'  => \$changeAmount,
                    'status'         => 'completed',
                ]);
PHP;

$newCreate = <<<PHP
                \$trx = Transaction::create([
                    'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'user_id'        => auth()->id(),
                    'total_amount'   => \$totalCalculated,
                    'payment_method' => \$request->payment_method,
                    'order_type'     => \$request->input('order_type', 'dine_in'),
                    'paid_amount'    => \$paidAmount,
                    'change_amount'  => \$changeAmount,
                    'status'         => 'completed',
                ]);
PHP;

$content = str_replace($oldCreate, $newCreate, $content);

file_put_contents('app/Http/Controllers/PosController.php', $content);
echo "PosController updated.\n";
