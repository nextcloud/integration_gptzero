<template>
	<NcAppNavigation>
		<NcAppNavigationCaption
			:title="historyTitle">
			<template #actions>
				<NcActionButton @click="saveHistoryToLocalStorage">
					<template #icon>
						<ContentSaveAll :size="20" />
					</template>
					{{ t('integration_gtpzero', 'Save history') }}
				</NcActionButton>
				<NcActionButton @click="clearHistory">
					<template #icon>
						<NotificationClearAll :size="20" />
					</template>
					{{ t('integration_gtpzero', 'Clear history') }}
				</NcActionButton>
			</template>
		</NcAppNavigationCaption>
		<template v-if="predictResultsHistory.length > 0" #list>
			<NcAppNavigationItem v-for="historyItem in predictResultsHistory"
				:key="historyItemKey(historyItem)"
				:exact="exactHistoryItem(historyItem)"
				:title="historyItemTitle(historyItem)"
				@click.prevent="loadHistoryItem(historyItem)">
				<template #actions>
					<NcActionButton @click="deleteHistoryItem(historyItem)">
						<template #icon>
							<span class="material-icon icon-delete" />
						</template>
						{{ t('integration_gtpzero', 'Delete') }}
					</NcActionButton>
				</template>
				<template #icon>
					<ClipboardTextClockOutline :size="18" />
				</template>
			</NcAppNavigationItem>
		</template>
		<template v-else #list>
			<NcEmptyContent :title="t('integration_gprzero', 'History is empty')">
				<template #icon>
					<FormatListBulleted />
				</template>
			</NcEmptyContent>
		</template>
		<template #footer>
			<ul class="app-navigation-entry__settings">
				<NcAppNavigationItem :title="t('integration_gptzero', 'GPTZero website')"
					href="https://gptzero.me/">
					<template #icon>
						<OpenInNew :size="20" />
					</template>
				</NcAppNavigationItem>
			</ul>
		</template>
	</NcAppNavigation>
</template>

<script>
import NcAppNavigation from '@nextcloud/vue/dist/Components/NcAppNavigation.js'
import NcAppNavigationCaption from '@nextcloud/vue/dist/Components/NcAppNavigationCaption.js'
import NcAppNavigationItem from '@nextcloud/vue/dist/Components/NcAppNavigationItem.js'
import NcEmptyContent from '@nextcloud/vue/dist/Components/NcEmptyContent.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'

import FormatListBulleted from 'vue-material-design-icons/FormatListBulleted.vue'
import ContentSaveAll from 'vue-material-design-icons/ContentSaveAll.vue'
import NotificationClearAll from 'vue-material-design-icons/NotificationClearAll.vue'
import OpenInNew from 'vue-material-design-icons/OpenInNew.vue'
import ClipboardTextClockOutline from 'vue-material-design-icons/ClipboardTextClockOutline.vue'

export default {
	name: 'GPTZeroNav',
	components: {
		NcAppNavigation,
		NcAppNavigationCaption,
		NcAppNavigationItem,
		NcEmptyContent,
		NcActionButton,
		FormatListBulleted,
		ContentSaveAll,
		NotificationClearAll,
		OpenInNew,
		ClipboardTextClockOutline,
	},
	props: {
		predictResultsHistory: {
			type: Array,
			default: () => [],
		},
		text: {
			type: String,
			default: '',
		},
		selectedFiles: {
			type: Array,
			default: () => [],
		},
	},
	data() {
		return {
		}
	},
	computed: {
		historyTitle() {
			return t('integration_gptzero', 'GPTZero History {count}/7', { count: this.predictResultsHistory.length })
		},
	},
	methods: {
		loadHistoryItem(historyPredictResult) {
			this.$emit('load-history-item', historyPredictResult)
		},
		deleteHistoryItem(historyItem) {
			this.$emit('delete-history-item', historyItem)
		},
		saveHistoryToLocalStorage() {
			this.$emit('save-history-to-local-storage')
		},
		clearHistory() {
			this.$emit('clear-history')
		},
		historyItemKey(historyItem) {
			if ('text' in historyItem) {
				return historyItem.text
			}
			if ('selectedFiles' in historyItem) {
				return historyItem.selectedFiles.join(',')
			}
		},
		exactHistoryItem(historyItem) {
			if ('text' in historyItem) {
				return historyItem.text === this.text
			}
			if ('selectedFiles' in historyItem) {
				return this.selectedFiles.map(f => f.fileid).join(', ') === historyItem.selectedFiles.map(f => f.fileid).join(',')
			}
		},
		historyItemTitle(historyItem) {
			if ('text' in historyItem) {
				return historyItem.text.substring(0, 50)
			}
			if ('selectedFiles' in historyItem) {
				return 'Files (' + historyItem.selectedFiles.length + '): ' + historyItem.selectedFiles.map(f => f.name).join(', ').substring(0, 50)
			}
		},
	},
}
</script>

<style lang="scss" scoped>
.app-navigation-entry__settings {
	height: auto !important;
	overflow: hidden !important;
	padding-top: 0 !important;
	flex: 0 0 auto;
}
</style>
