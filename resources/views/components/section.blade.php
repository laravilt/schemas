<x-laravilt-component name="section" :data="$component->toLaraviltProps()">
    <div
        class="section"
        :class="{ 'dark': theme === 'dark' }"
        :dir="rtl ? 'rtl' : 'ltr'"
    >
        <div v-if="heading" class="section-header" :class="{ 'cursor-pointer': collapsible }" @click="toggleCollapse">
            <div class="flex items-center gap-3">
                <span v-if="icon" v-html="icon" class="section-icon"></span>
                <div class="flex-1">
                    <h3 class="section-heading" v-text="heading"></h3>
                    <p v-if="description" class="section-description" v-text="description"></p>
                </div>
                <button v-if="collapsible" type="button" class="collapse-toggle">
                    <svg v-if="!isCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <div v-show="!isCollapsed" class="section-content">
            <component
                v-for="(child, index) in schema"
                :key="index"
                :is="child.component || 'div'"
                v-bind="child"
            />
        </div>
    </div>
</x-laravilt-component>

<script>
export default {
    props: {
        heading: String,
        description: String,
        icon: String,
        collapsible: Boolean,
        collapsed: Boolean,
        schema: Array,
        rtl: Boolean,
        theme: String
    },
    data() {
        return {
            isCollapsed: this.collapsed
        }
    },
    methods: {
        toggleCollapse() {
            if (this.collapsible) {
                this.isCollapsed = !this.isCollapsed;
            }
        }
    }
}
</script>
