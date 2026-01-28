<script setup lang="ts">
import TransactionTable from '@/components/tranasction-components/TransactionTable.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import { Transaction } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps<{
    transactions: Transaction[],
    filters: {
        search?: string
    }
}>();

const search = ref(props.filters.search ?? '');

watch(search, (value) => {
    router.get(
        '/transaction', // match sa route mo sa web.php
        { search: value ?? '' },
        { preserveState: true, replace: true }
    );
});

</script>

<template>
    <Head title="Transaction" />
    <AppLayout>
        <div class="flex flex-1 flex-col gap-4 p-4">
            <section class="flex items-center justify-between">
                <div class="relative w-120">
                    <Search class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                    <Input v-model="search" class="pl-10" placeholder="Search..." />
                </div>
            </section>
            <section>
                <TransactionTable :transaction="props.transactions"/>
            </section>
        </div>
    </AppLayout>
</template>
