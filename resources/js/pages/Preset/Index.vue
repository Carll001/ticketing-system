<script setup lang="ts">
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
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
import preset from '@/routes/preset';
import { Preset } from '@/types';

const props = defineProps<{
    presets: Preset[],
}>();

const createPreset = () => {
    router.visit(preset.create().url)
}

const deletePreset = (id: string) => {
    router.delete(preset.delete(id).url)
}

const editPreset = (id: string) => {
    router.visit(preset.edit(id).url)
}

const viewPreset = (id: string) => {
    router.visit(preset.show(id).url)
}

</script>
<template>

    <Head title="Preset" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <section class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl">Your Preset</h3>
                    <p class="text-sm text-muted-foreground">your preset</p>
                </div>
                <div>
                    <Button size="sm" @click="createPreset">Create Preset</Button>
                </div>
            </section>
            <section>
                <Table>
                    <TableCaption>A list of your recent invoices.</TableCaption>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]">
                                Name
                            </TableHead>
                            <TableHead class="text-right">
                                Action
                            </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="preset in presets">
                            <TableCell class="font-medium">
                                {{ preset.name }}
                            </TableCell>
                            <TableCell class="text-right space-x-2">
                                <Button size="sm" variant="destructive" @click="deletePreset(preset.id)">Delete</Button>
                                <Button size="sm" variant="secondary" @click="editPreset(preset.id)">Edit</Button>
                                <Button size="sm" variant="default" @click="viewPreset(preset.id)">View</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                    <!-- <TableBody>
                        <TableRow  >
                            <TableCell class="font-medium">
                                ads
                            </TableCell>
                            <TableCell class="text-right space-x-2">
                                <Button size="sm" variant="destructive">Delete</Button>
                                <Button size="sm" variant="secondary">Edit</Button>
                                <Button size="sm" variant="default">View</Button>
                            </TableCell>
                        </TableRow>
                    </TableBody> -->
                    <TableFooter>
                        <!-- <TableRow>
                            <TableCell colspan="">
                                Total
                            </TableCell>
                            <TableCell class="text-right">
                                $2,500.00
                            </TableCell>
                        </TableRow> -->
                    </TableFooter>
                </Table>
            </section>
        </div>
    </AppLayout>
</template>