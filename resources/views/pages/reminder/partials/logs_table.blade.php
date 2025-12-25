@forelse($reminders as $reminder)
<tr class="hover:bg-slate-50 transition">
    <td class="px-6 py-4">
        <div class="font-bold text-[#84994F]">{{ $reminder->customer->name }}</div>
        <div class="text-xs text-slate-400">{{ $reminder->customer->phone }}</div>
    </td>
    <td class="px-6 py-4">
        <a href="{{ route('orders.show', $reminder->order_id) }}" class="inline-flex items-center px-2 py-0.5 rounded border border-slate-200 bg-slate-50 text-xs font-mono text-slate-600 hover:border-blue-300 hover:text-blue-600 transition">
            {{ $reminder->order->order_number }}
        </a>
    </td>
    <td class="px-6 py-4">
        <div class="flex flex-col">
            <span class="font-bold text-slate-700 {{ $reminder->scheduled_at->isPast() && $reminder->status == 'pending' ? 'text-red-500' : '' }}">
                {{ $reminder->scheduled_at->format('d M Y') }}
            </span>
            <span class="text-xs text-slate-400">{{ $reminder->scheduled_at->format('H:i') }}</span>
        </div>
    </td>
    <td class="px-6 py-4 text-slate-500 max-w-xs truncate" title="{{ $reminder->reminder->message_template }}">
        {{ $reminder->reminder->message_template }}
    </td>
    <td class="px-6 py-4 text-center">
        @if($reminder->status == 'sent')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold">Terkirim</span>
        @elseif($reminder->status == 'failed')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-red-100 text-red-700 text-[10px] font-bold">Gagal</span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-yellow-50 text-yellow-700 text-[10px] font-bold border border-yellow-100">Menunggu</span>
        @endif
    </td>
    <td class="px-6 py-4 text-right">
        @if($reminder->status == 'pending')
            <form action="{{ route('reminders.destroy', $reminder->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Batalkan pengingat ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </form>
        @else
            <span class="text-slate-300">-</span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada jadwal pengingat.</td>
</tr>
@endforelse

@if($reminders->hasPages())
<tr>
    <td colspan="6" class="p-4 border-t border-slate-50 bg-slate-50/50">
        {{ $reminders->links() }}
    </td>
</tr>
@endif
