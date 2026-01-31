<script setup lang="ts">
import { FileQuestion } from 'lucide-vue-next';
import EmptyData from '../EmptyData.vue';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Checkbox } from '../ui/checkbox';
import { Input } from '../ui/input';
import { Textarea } from '../ui/textarea';
import { computed } from 'vue';
import { Field, Step } from '@/types';
import { useForm, usePage } from '@inertiajs/vue3';

// FOR CURRENTLY AUTHENTICATED USER
const page = usePage();
const auth = computed(() => page.props.auth);

const props = defineProps<{
    step: Step
}>();

const groupedFields = computed(() => {
  const fields = props.step.fields || [];

  return fields.reduce((acc, field) => {
    const type = field.type;
    if (!acc[type]) acc[type] = [];
    acc[type].push(field);
    return acc;
  }, {} as Record<string, Field[]>);
});

const form = useForm<{
  step_field_id: string | number;
  response: Record<string, any>;
}>({
  step_field_id: props.step.id,
  response: (props.step.fields || []).reduce((acc, field) => {
    const existingValue = field.responses?.[0]?.response;

    if (field.type === 'Checkbox') {
      acc[field.id] = existingValue !== undefined ? existingValue : "0";
    } else {
      acc[field.id] = existingValue || '';
    }
    return acc;
  }, {} as Record<string, any>)
});


</script>
<template>
    <Card>
        <CardHeader>
            <CardTitle>Fields</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="space-y-4">
                <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
                    <Label
                        class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
                        {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
                    </Label>

                    <div class="space-y-4 pl-2">
                        <div v-for="field in fields" :key="field.id" class="space-y-4">
                            <div class="flex gap-3"
                                :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                                <div :class="[type === 'Checkbox' ? 'w-auto' : 'w-full order-2']">
                                    <Input v-if="type === 'Input'" v-model="form.response[field.id]"
                                        :readonly="auth.user.id === props.step.task.creator_id"
                                        :disabled="props.step.status === 'completed'"
                                        :placeholder="`Enter ${field.label.toLowerCase()}...`"
                                        class="h-8 text-xs bg-zinc-900/50" />

                                    <Textarea v-else-if="type === 'Description'" v-model="form.response[field.id]"
                                        :readonly="auth.user.id === props.step.task.creator_id"
                                        :disabled="props.step.status === 'completed'"
                                        :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                                        class="min-h-[60px] text-xs bg-zinc-900/50 resize-none" />

                                    <div v-else-if="type === 'Checkbox'" class="flex items-center">
                                        <Checkbox :id="field.id" :model-value="form.response[field.id] === 'true'"
                                            :disabled="auth.user.id === props.step.task.creator_id || props.step.status === 'completed'"
                                            @update:model-value="(val) => form.response[field.id] = val ? 'true' : 'false'" />
                                    </div>
                                </div>

                                <Label :class="[
                                    'text-xs font-medium text-zinc-300',
                                    type === 'Checkbox' ? 'order-2' : 'order-1'
                                ]">
                                    {{ field.label }}
                                </Label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-xs text-zinc-600 text-center mx-auto">
                <EmptyData :icon="FileQuestion" title="no fields yet" message="No fields configured for this step."
                    :length="props.step.fields?.length === 0" />
            </div>
        </CardContent>
    </Card>
</template>