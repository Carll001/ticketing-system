<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/AppLayout.vue';
import preset from '@/routes/preset';
import { Field, Preset } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { setCommentRange } from 'typescript';
import { computed } from 'vue';

const props = defineProps<{
    preset: Preset
}>();

// Simplified grouping: only group fields for the current step
const groupedFields = computed(() => {
    const fields = props.preset.fields || [];

    return fields.reduce((acc, field) => {
        const type = field.type;
        if (!acc[type]) acc[type] = [];
        acc[type].push(field);
        return acc;
    }, {} as Record<string, Field[]>);
});

const editField = () => {
    router.visit(preset.edit(props.preset.id).url)
}
</script>
<template>

    <Head title="SHOW PRESET" />
    <AppLayout>
        <div class="flex flex-col flex-1 gap-4 p-4">
            <section class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg">{{ props.preset.name }}</h3>
                    <p class="text-sm text-muted-foreground">{{ props.preset.description }}</p>
                </div>
                <div class="space-x-2">
                    <Button size="sm" variant="destructive">Delete</Button>
                    <Button size="sm" variant="secondary" @click="editField">Edit</Button>
                </div>
            </section>
            <section>
                <div class="space-y-8">
                    <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4 ">
                        <Label
                            class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
                            {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                        </Label>

                        <div class="space-y-4 pl-2">
                            <section v-for="field in fields" :key="field.id" class="space-y-4">
                                <div class="flex gap-3"
                                    :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                                    <section :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                        <Input v-if="type === 'Input'"
                                            :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                            class="h-8 text-xs bg-zinc-900/50" readonly/>
                                        <Textarea v-else-if="type === 'Description'"
                                            :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                                            class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" readonly/>

                                        <div v-else-if="type === 'Checkbox'" class="flex items-center">
                                            <Checkbox disabled/>
                                        </div>
                                    </section>
                                    <Label :class="[
                                        'text-xs font-medium text-zinc-300',
                                        type === 'Checkbox' ? 'order-2' : 'order-1'
                                    ]">
                                        {{ field.label }}
                                    </Label>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </section>
            <!-- <div>
                <pre>{{ preset.fields }}</pre>
            </div> -->
        </div>
    </AppLayout>
</template>