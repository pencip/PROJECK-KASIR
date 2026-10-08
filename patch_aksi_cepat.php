<?php
$content = file_get_contents('resources/views/manager/dashboard.blade.php');

$oldIcons = <<<HTML
            {{-- Action Icons --}}
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; padding:0 8px;">
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Buka POS">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; color:#f59e0b;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Menu">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; color:#60a5fa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Promo">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; color:#f87171;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </div>
                <div style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Laporan">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(139,92,246,0.15); display:flex; align-items:center; justify-content:center; color:#a78bfa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <button class="tbtn" style="flex:1; justify-content:center;">Kelola</button>
                <button class="tbtn accent" style="flex:1; justify-content:center; background:#111827; border-color:#374151;">Buka Kasir →</button>
            </div>
HTML;

$newIcons = <<<HTML
            {{-- Action Icons --}}
            <div style="display:flex; justify-content:space-between; margin-bottom:24px; padding:0 8px;">
                <a href="{{ route('pos.index') }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Buka POS">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; color:#f59e0b;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                    </div>
                </a>
                <a href="{{ auth()->user()->role === 'admin' ? route('admin.menus.index') : '#' }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Menu">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; color:#60a5fa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                    </div>
                </a>
                <a href="{{ route('manager.promotions.index') }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Promo">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; color:#f87171;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                </a>
                <a href="{{ route('manager.transactions') }}" style="display:flex; flex-direction:column; align-items:center; gap:6px; cursor:pointer;" title="Laporan">
                    <div style="width:36px; height:36px; border-radius:50%; background:rgba(139,92,246,0.15); display:flex; align-items:center; justify-content:center; color:#a78bfa;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                </a>
            </div>

            <div style="display:flex; gap:10px;">
                <a href="{{ route('manager.transactions') }}" class="tbtn" style="flex:1; justify-content:center; text-decoration:none;">Riwayat</a>
                <a href="{{ route('pos.index') }}" class="tbtn accent" style="flex:1; justify-content:center; background:#111827; border-color:#374151; text-decoration:none;">Buka Kasir →</a>
            </div>
HTML;

$content = str_replace($oldIcons, $newIcons, $content);

file_put_contents('resources/views/manager/dashboard.blade.php', $content);
echo "Blade updated with working links.\n";
