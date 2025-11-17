<template>
    <div class="tabs" :dir="rtl ? 'rtl' : 'ltr'">
        <!-- Tab Headers -->
        <div class="tab-headers" role="tablist">
            <button
                v-for="(tab, index) in tabs"
                :key="index"
                type="button"
                role="tab"
                :aria-selected="currentTab === index"
                :class="['tab-header', { 'active': currentTab === index }]"
                @click="selectTab(index)"
            >
                <span v-if="tab.icon" v-html="tab.icon" class="tab-icon"></span>
                <span v-text="tab.label"></span>
                <span v-if="tab.badge" class="tab-badge" v-text="tab.badge"></span>
            </button>
        </div>

        <!-- Tab Panels -->
        <div class="tab-panels">
            <div
                v-for="(tab, index) in tabs"
                :key="index"
                v-show="currentTab === index"
                role="tabpanel"
                class="tab-panel"
            >
                <component
                    v-for="(child, childIndex) in tab.schema"
                    :key="childIndex"
                    :is="child.component || 'div'"
                    v-bind="child"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    tabs: {
        type: Array,
        default: () => []
    },
    activeTab: {
        type: Number,
        default: 0
    },
    persistTabInQueryString: {
        type: Boolean,
        default: false
    },
    rtl: {
        type: Boolean,
        default: false
    },
    theme: {
        type: String,
        default: 'light'
    }
});

const currentTab = ref(props.activeTab);

const selectTab = (index) => {
    currentTab.value = index;

    if (props.persistTabInQueryString) {
        const url = new URL(window.location);
        url.searchParams.set('tab', index);
        history.pushState({}, '', url);
    }
};

onMounted(() => {
    if (props.persistTabInQueryString) {
        const url = new URL(window.location);
        const tabFromQuery = url.searchParams.get('tab');
        if (tabFromQuery !== null) {
            currentTab.value = parseInt(tabFromQuery);
        }
    }
});
</script>
