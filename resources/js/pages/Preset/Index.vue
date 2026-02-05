<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import AppLayout from '@/layouts/AppLayout.vue';
import preset from '@/routes/preset';
import { Preset, type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Eye, Pencil, Search, Trash } from 'lucide-vue-next';

const props = defineProps<{
    presets: {
        data: Preset[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
    };
    filters?: { search?: string };
}>();

import { ref, watch } from 'vue';
import { toast } from 'vue-sonner';

const search = ref(props.filters?.search ?? '');
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const performSearch = (page = 1) => {
    router.get(
        preset.index().url,
        { search: search.value, page },
        {
            preserveState: true,
            replace: true,
        },
    );
};

const goToPage = (page: number) => {
    if (page >= 1 && page <= props.presets.last_page) {
        router.get(
            preset.index().url,
            { page, search: search.value || undefined },
            { preserveState: true, replace: true },
        );
    }
};

// watch search changes and trigger backend query (matches Department.vue behavior)
watch(search, (value) => {
    router.get(
        preset.index().url,
        { search: value ?? '' },
        { preserveState: true, replace: true },
    );
});

const createPreset = () => {
    router.visit(preset.create().url);
};

const deletePreset = (id: string) => {
    router.delete(preset.delete(id).url);
};

const editPreset = (id: string) => {
    router.visit(preset.edit(id).url);
};

const viewPreset = (id: string) => {
    router.visit(preset.show(id).url);
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Presets',
        href: '',
    },
];

const show = ref(false);
const id = ref<string | null>(null);

const open = (presetId: string) => {
    id.value = presetId;
    show.value = true;
};

const remove = () => {
    if (!id.value) return;

    router.delete(preset.delete(id.value).url, {
        onSuccess: () => {
            toast.success('Deleted successfully');
        },
        onFinish: () => {
            show.value = false;
            id.value = null;
        },
    });
};
</script>

<template>
    <Head title="Preset" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-4 p-4">
            <section class="flex items-center justify-between">
                <div class="relative w-120">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        class="pl-10"
                        placeholder="Search..."
                        v-model="search"
                    />
                </div>
                <div>
                    <Button size="sm" @click="createPreset"
                        >Create Preset</Button
                    >
                </div>
            </section>
            <section
                class="flex h-126 flex-col overflow-hidden rounded-xl border shadow-sm"
            >
                <Table class="flex-1">
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-[100px]"> #</TableHead>
                            <TableHead class="w-[100px]"> Name </TableHead>
                            <TableHead class="text-right"> Action </TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="(preset, index) in presets.data">
                            <TableCell class="">
                                {{ index + 1 }}
                            </TableCell>
                            <TableCell class="max-w-lg truncate font-medium">
                                {{ preset.name }}
                            </TableCell>
                            <TableCell class="space-x-2 text-right">
                                <Button
                                    size="sm"
                                    variant="outline"
                                    @click="viewPreset(preset.id)"
                                >
                                    <Eye class="h-3 w-3" /> View</Button
                                >
                                <Button
                                    size="sm"
                                    variant="secondary"
                                    @click="editPreset(preset.id)"
                                >
                                    <Pencil class="h-3 w-3" /> Edit</Button
                                >
                                <!-- <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="deletePreset(preset.id)"
                                >
                                    <Trash class="h-3 w-3" />Delete</Button
                                > -->

                                <Button
                                    size="sm"
                                    variant="destructive"
                                    @click="open(preset.id)"
                                >
                                    <Trash class="h-3 w-3" /> Delete
                                </Button>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>

                <!-- no result -->
                <section>
                    <div
                        v-if="
                            props.presets &&
                            props.presets.data &&
                            props.presets.data.length === 0
                        "
                        class="p-6 text-center text-sm text-muted-foreground"
                    >
                        No results found.
                    </div>
                </section>

                <!-- pagination here -->
                <div class="mt-auto border-t px-6 py-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm">
                            Showing {{ presets.from }} to {{ presets.to }} of
                            {{ presets.total }} presets
                        </p>
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="presets.current_page === 1"
                                @click="goToPage(presets.current_page - 1)"
                                >Previous</Button
                            >
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="
                                    presets.current_page === presets.last_page
                                "
                                @click="goToPage(presets.current_page + 1)"
                                >Next</Button
                            >
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <div>
            <Dialog :open="show" @update:open="show = false">
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Delete Preset</DialogTitle>
                        <DialogDescription>
                            Are you sure you want to delete this preset? This
                            action cannot be undone.
                        </DialogDescription>
                    </DialogHeader>

                    <DialogFooter>
                        <Button variant="outline" @click="show = false"
                            >Cancel</Button
                        >
                        <Button variant="destructive" @click="remove"
                            >Delete</Button
                        >
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
