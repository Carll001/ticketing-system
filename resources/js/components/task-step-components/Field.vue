<script setup lang="ts">
import { FileQuestion } from 'lucide-vue-next';
import EmptyData from '../EmptyData.vue';
import { Card, CardContent, CardHeader, CardTitle } from '../ui/card';
import { Checkbox } from '../ui/checkbox';
import { Input } from '../ui/input';
import { Label } from '../ui/label';
import { Textarea } from '../ui/textarea';
import { computed } from 'vue';
import { Field, Step } from '@/types';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const auth = computed(() => page.props.auth);

const props = defineProps<{
  step: Step;
  form: any; // The form instance from parent component
}>();

// Group fields by type for better organization
const groupedFields = computed(() => {
  const fields = props.step.fields || [];

  return fields.reduce((acc, field) => {
    const type = field.type;
    if (!acc[type]) acc[type] = [];
    acc[type].push(field);
    return acc;
  }, {} as Record<string, Field[]>);
});

const isCreator = auth.value.user.id === props.step.task.creator_id;
const isCompleted = props.step.status === 'completed';

// Check if user can edit this step
const canEdit = computed(() => {
  return !isCreator && !isCompleted;
});

// Check if fields should be readonly
const isReadonly = computed(() => {
  return auth.value.user.id === props.step.task.creator_id;
});

// Check if fields should be disabled
const isDisabled = computed(() => {
  return props.step.status === 'completed';
});

// Handle checkbox value changes
const handleCheckboxChange = (fieldId: string | number, value: boolean | string) => {
  // Ensure value is treated as boolean
  const boolValue = typeof value === 'string' ? value === 'true' : Boolean(value);
  props.form.response[fieldId] = boolValue ? 'true' : 'false';
};

// Get checkbox checked state
const getCheckboxValue = (fieldId: string | number): boolean => {
  const value = props.form.response[fieldId];
  if (typeof value === 'boolean') return value;
  if (typeof value === 'string') return value === 'true' || value === '1';
  return Boolean(value);
};
</script>

<template>
  <Card>
    <CardHeader>
      <CardTitle>Fields</CardTitle>
    </CardHeader>
    <CardContent>
      <!-- Fields Display -->
      <div v-if="Object.keys(groupedFields).length > 0" class="space-y-4">
        <div v-for="(fields, type) in groupedFields" :key="type" class="space-y-4">
          <!-- Type Header -->
          <Label
            class="text-[10px] uppercase font-black text-zinc-500 tracking-[0.2em] border-b border-zinc-800 pb-1 block">
            {{ type }}{{ type === 'Checkbox' ? 'es' : 's' }}
          </Label>

          <!-- Fields of this type -->
          <div class="space-y-4 pl-2">
            <div v-for="field in fields" :key="field.id" class="space-y-2">
              <div class="flex gap-3" :class="type === 'Checkbox' ? 'flex-row items-center' : 'flex-col items-start'">
                <!-- Label (order changes based on field type) -->
                <Label :for="String(field.id)" :class="[
                  'text-xs font-medium text-zinc-300',
                  type === 'Checkbox' ? 'order-2 cursor-pointer' : 'order-1'
                ]">
                  {{ field.label }}
                </Label>

                <!-- Field Input -->
                <div :class="[type === 'Checkbox' ? 'w-auto order-1' : 'w-full order-2']">
                  <!-- Text Input -->
                  <Input v-if="type === 'Input'" :id="String(field.id)" v-model="form.response[field.id]"
                    :readonly="isReadonly" :disabled="isDisabled" :placeholder="`Enter ${field.label.toLowerCase()}...`"
                    class="h-8 text-xs " />

                  <!-- Textarea -->
                  <Textarea v-else-if="type === 'Description'" :id="String(field.id)" v-model="form.response[field.id]"
                    :readonly="isReadonly" :disabled="isDisabled"
                    :placeholder="`Provide details for ${field.label.toLowerCase()}...`"
                    class="min-h-[60px] text-xs  resize-none" />

                  <!-- Checkbox -->
                  <div v-else-if="type === 'Checkbox'" class="flex items-center">
                    <Checkbox :id="String(field.id)" :model-value="getCheckboxValue(field.id)"
                      :disabled="isReadonly || isDisabled"
                      @update:model-value="(val) => handleCheckboxChange(field.id, val)" />
                  </div>
                </div>
              </div>

              <!-- Show existing response indicator (optional) -->
              <div v-if="field.responses && field.responses.length > 0 && type !== 'Checkbox'"
                class="text-[10px] text-zinc-600 pl-1">
                Last updated: {{ new Date(field.responses[0].updated_at ||
                  field.responses[0].created_at).toLocaleString() }}
              </div>
            </div>
          </div>
        </div>
        <div v-if="step.has_cost" class="space-y-2 pt-4 border-t border-zinc-800">
          <Label>Cost</Label>
          <Input type="number" v-model="form.cost" placeholder="Enter cost amount..."
            class="h-8 text-xs " :readonly="isCreator" />
        </div>
      </div>





      <!-- Helper Text for Read-only State -->
      <div v-if="isReadonly && props.step.fields && props.step.fields.length > 0"
        class="mt-4 text-xs text-amber-600 bg-amber-950/20 border border-amber-900/30 rounded-md p-2">
        <strong>Read-only:</strong> As the task creator, you can view but not edit responses.
      </div>

      <!-- Helper Text for Completed State -->
      <div v-if="isDisabled && props.step.fields && props.step.fields.length > 0"
        class="mt-4 text-xs text-blue-600 bg-blue-950/20 border border-blue-900/30 rounded-md p-2">
        <strong>Completed:</strong> This step has been marked as completed and cannot be edited.
      </div>
    </CardContent>
  </Card>
</template>