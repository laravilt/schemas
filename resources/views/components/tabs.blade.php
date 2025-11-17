<x-laravilt-component name="tabs" :data="$component->toLaraviltProps()">
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
</x-laravilt-component>

<script>
export default {
    props: {
        tabs: Array,
        activeTab: Number,
        persistTabInQueryString: Boolean,
        rtl: Boolean,
        theme: String
    },
    data() {
        return {
            currentTab: this.activeTab
        }
    },
    methods: {
        selectTab(index) {
            this.currentTab = index;

            if (this.persistTabInQueryString) {
                const url = new URL(window.location);
                url.searchParams.set('tab', index);
                history.pushState({}, '', url);
            }
        }
    },
    mounted() {
        if (this.persistTabInQueryString) {
            const url = new URL(window.location);
            const tabFromQuery = url.searchParams.get('tab');
            if (tabFromQuery !== null) {
                this.currentTab = parseInt(tabFromQuery);
            }
        }
    }
}
</script>
