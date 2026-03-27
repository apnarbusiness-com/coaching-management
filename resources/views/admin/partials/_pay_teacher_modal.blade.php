<div id="payModal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">Add Payment</h3>
                <button onclick="closePayModal()" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Teacher: <span id="payModalTeacherName" class="font-medium text-primary"></span>
            </p>
        </div>
        
        <form id="payModalForm" method="POST" class="p-6 space-y-4">
            @csrf
            
            <input type="hidden" name="teachers_payment_id" id="payModalPaymentId">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Amount</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">৳</span>
                        <input type="number" name="amount" id="payModalAmount" step="0.01" min="0.01"
                            class="w-full pl-8 pr-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary"
                            required>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Remaining: <span id="payModalRemaining" class="font-medium"></span></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Payment Date</label>
                    <input type="date" name="payment_date" 
                        value="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary"
                        required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Payment Method</label>
                    <select name="payment_method" 
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary"
                        required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="mobile_banking">Mobile Banking</option>
                    </select>
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Reference (Optional)</label>
                    <input type="text" name="reference" placeholder="Transaction/reference number"
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary">
                </div>
                
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" placeholder="Any additional notes..."
                        class="w-full px-4 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-primary"></textarea>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closePayModal()"
                    class="px-4 py-2 rounded-lg text-slate-600 dark:text-slate-300 font-medium hover:bg-slate-100 dark:hover:bg-slate-700">
                    Cancel
                </button>
                <button type="submit"
                    class="px-6 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90">
                    Add Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPayModal(paymentId, teacherName, totalAmount, paidAmount, remainingAmount) {
    document.getElementById('payModal').classList.remove('hidden');
    document.getElementById('payModalPaymentId').value = paymentId;
    document.getElementById('payModalTeacherName').textContent = teacherName;
    document.getElementById('payModalRemaining').textContent = '৳' + remainingAmount.toFixed(2);
    document.getElementById('payModalAmount').value = remainingAmount.toFixed(2);
    document.getElementById('payModalAmount').max = remainingAmount.toFixed(2);
    
    // Set form action
    const form = document.getElementById('payModalForm');
    form.action = '/admin/teachers-payments/' + paymentId + '/transactions';
}

function closePayModal() {
    document.getElementById('payModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePayModal();
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePayModal();
    }
});
</script>
