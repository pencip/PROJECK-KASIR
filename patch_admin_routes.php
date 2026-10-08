<?php
$content = file_get_contents('routes/web.php');

// Fix root redirect for admin
$oldRedirect = "'admin'   => redirect()->route('admin.menus.index'),";
$newRedirect = "'admin'   => redirect()->route('admin.dashboard'),";
$content = str_replace($oldRedirect, $newRedirect, $content);

// Add use statement for Admin\DashboardController
$useAdminDash = "use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;\n";
if (strpos($content, $useAdminDash) === false) {
    $content = str_replace("use App\Http\Controllers\Admin\MenuController;", $useAdminDash . "use App\Http\Controllers\Admin\MenuController;", $content);
}

// Add route inside admin group
$oldGroup = "Route::resource('menus',      MenuController::class);";
$newGroup = "Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');\n        Route::resource('menus',      MenuController::class);";
$content = str_replace($oldGroup, $newGroup, $content);

file_put_contents('routes/web.php', $content);
echo "Admin routes updated.\n";
