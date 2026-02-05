<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    Table,
    TableBody,
    TableCaption,
    TableCell,
    TableFooter,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table'
import { Step, User } from '@/types';
import { Input } from '@/components/ui/input';
import { Search } from 'lucide-vue-next';
import { ref, computed } from 'vue';

interface RejectedStep {
    id: string
    rejected_by: User
    reason: string
    step: Step
    created_at: string
}

const props = defineProps<{
    rejectedSteps: RejectedStep[]
}>();

const searchQuery = ref('');

const filteredSteps = computed(() => {
    if (!searchQuery.value.trim()) {
        return props.rejectedSteps;
    }
    
    const query = searchQuery.value.toLowerCase();
    return props.rejectedSteps.filter(step =>
        step.id.toLowerCase().includes(query) ||
        step.rejected_by.name.toLowerCase().includes(query) ||
        step.step.title.toLowerCase().includes(query)
    );
});

</script>
<template>

    <Head title="rejected step" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="w-lg">
              <div class="relative w-120">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="searchQuery"
                        class="pl-10"
                        placeholder="Search..."
                    />
                </div>
            </div>
            <Table>
                <TableCaption>A list of your recent invoices.</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[100px]">
                            ID
                        </TableHead>
                        <TableHead>Rejected by</TableHead>
                        <TableHead>Step title</TableHead>
                        <TableHead class="text-right">
                            Rejected Date
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="rejectedStep in filteredSteps" :key="rejectedStep.id">
                        <TableCell class="font-medium">
                            {{ rejectedStep.id }}
                        </TableCell>
                        <TableCell>{{rejectedStep.rejected_by.name}}</TableCell>
                        <TableCell>{{rejectedStep.step.title}}</TableCell>
                        <TableCell class="text-right">
                            {{ new Date(rejectedStep.created_at).toLocaleDateString() }}
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>
    </AppLayout>
</template>