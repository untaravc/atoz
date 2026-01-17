<template>
    <Backoffice>
        <section class="grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">Net revenue</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">$82,410</p>
                <p class="mt-2 text-xs text-emerald-500">Up 12.4% this month</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">Open tasks</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">18</p>
                <p class="mt-2 text-xs text-amber-500">5 need review today</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm font-semibold text-slate-500">Runway</p>
                <p class="mt-3 text-3xl font-semibold text-slate-900">7.8 months</p>
                <p class="mt-2 text-xs text-slate-400">Updated 2 hours ago</p>
            </div>
        </section>

        <section class="mt-8 grid gap-6 lg:grid-cols-[2fr_1fr]">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Recent activity</h2>
                    <a class="text-sm font-semibold text-blue-600 hover:text-blue-500" href="#">View all</a>
                </div>
                <div class="mt-6 space-y-4 text-sm text-slate-600">
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="font-semibold text-slate-900">Invoice #2041 paid</p>
                            <p class="text-xs text-slate-400">Bluefield Media · 2 hours ago</p>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-600">+ $4,200</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="font-semibold text-slate-900">Forecast updated</p>
                            <p class="text-xs text-slate-400">Scenario B · 5 hours ago</p>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-500">Ready</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                        <div>
                            <p class="font-semibold text-slate-900">Payroll processed</p>
                            <p class="text-xs text-slate-400">May payroll · Yesterday</p>
                        </div>
                        <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-600">Pending</span>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Notifications</h2>
                    <ul class="mt-4 space-y-3 text-sm text-slate-600">
                        <li class="rounded-xl border border-slate-200 px-4 py-3">New expense report submitted.</li>
                        <li class="rounded-xl border border-slate-200 px-4 py-3">Vendor contract expires in 10 days.</li>
                        <li class="rounded-xl border border-slate-200 px-4 py-3">Team meeting at 4:30 PM.</li>
                    </ul>
                </div>
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Quick actions</h2>
                    <div class="mt-4 grid gap-3 text-sm text-slate-600">
                        <button class="rounded-xl border border-slate-200 px-4 py-3 text-left hover:bg-slate-50">
                            Review expenses
                        </button>
                        <button class="rounded-xl border border-slate-200 px-4 py-3 text-left hover:bg-slate-50">
                            Approve invoices
                        </button>
                        <button class="rounded-xl border border-slate-200 px-4 py-3 text-left hover:bg-slate-50">
                            Update budgets
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </Backoffice>
</template>

<script setup>
import Backoffice from '../Backoffice.vue';
</script>
