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

const props = defineProps < {
    rejectedSteps: {
        data: {
            id: string
            rejected_by: User
            reason: string

            step: Step

            created_at: string
        }
    }
} > ();

</script>
<template>

    <Head title="rejected step" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <div class="w-lg">
                <Input/>
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
                    <TableRow v-for="rejectedStep in props.rejectedSteps">
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
        <!-- <pre>{{ props.rejectedSteps }}</pre> -->
        </div>
    </AppLayout>
</template>