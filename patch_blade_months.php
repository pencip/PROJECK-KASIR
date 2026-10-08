<?php
$content = file_get_contents('resources/views/manager/dashboard.blade.php');

$oldSelect = <<<HTML
                <select id="filterMonth" style="background:var(--bg-surface); border:1px solid var(--border-card); color:var(--text-secondary); border-radius:8px; padding:6px 10px; font-size:0.75rem; outline:none; cursor:pointer;">
                    <option value="10">Oktober 2026</option>
                    <option value="09">September 2026</option>
                </select>
HTML;

$newSelect = <<<HTML
                <select id="filterMonth" style="background:var(--bg-surface); border:1px solid var(--border-card); color:var(--text-secondary); border-radius:8px; padding:6px 10px; font-size:0.75rem; outline:none; cursor:pointer;">
                    @foreach(\$filterMonths as \$m => \$label)
                        <option value="{{ \$m }}">{{ \$label }}</option>
                    @endforeach
                </select>
HTML;

$content = str_replace($oldSelect, $newSelect, $content);

file_put_contents('resources/views/manager/dashboard.blade.php', $content);
echo "Blade updated with dynamic months.\n";
