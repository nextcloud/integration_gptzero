<template>
	<div class="scan-results">
		<div class="scan-results__heading">
			<div class="scan-results__heading_result">
				<p>
					{{ completelyGeneratedProbText() }}
				</p>
				<p class="scan-results__heading_result-subline">
					{{ t('integration_gptzero', 'The probability that an AI generated this text is:') }}
					{{ completely_generated_prob + '%' }}
				</p>
			</div>
			<h3 v-if="selectedFiles.length > 1" :title="documentFilePath">
				{{ documentName }}
				<NcButton v-if="selectedFiles.length > 0"
					:href="pathToFilesApp"
					target="_blank"
					type="tertiary">
					<template #icon>
						<OpenInNew :size="18" />
					</template>
				</NcButton>
			</h3>
		</div>
		<div class="overall-results">
			<p>
				{{ t('integration_gptzero', 'Average generated probability:') }}
				{{ average_generated_prob }}
				<NcProgressBar :value="Number(document.average_generated_prob)" :size="'small'" :max="100" />
			</p>
			<p style="font-size: 0.85em">
				{{ t('integration_gptzero', 'The average of the probabilties that each sentence was generated by an AI') }}
			</p>
		</div>
		<h3 class="document-sentences" @click="showSentences = !showSentences">
			{{ t('integration_gptzero', 'Sentences') }}
			<Triangle :size="12" :class="{'triangle-icon-rotate': showSentences}" />
		</h3>
		<div v-if="showSentences" class="sentences">
			<SentenceMark v-for="sentence in sentences"
				:key="sentence.id"
				:sentence="sentence" />
		</div>
	</div>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'

import NcProgressBar from '@nextcloud/vue/dist/Components/NcProgressBar.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'

import SentenceMark from './SentenceMark.vue'
import Triangle from 'vue-material-design-icons/Triangle.vue'
import OpenInNew from 'vue-material-design-icons/OpenInNew.vue'

export default {
	name: 'ScanResults',
	components: {
		NcProgressBar,
		NcButton,
		SentenceMark,
		Triangle,
		OpenInNew,
	},
	props: {
		document: {
			type: Object,
			required: true,
		},
		selectedFiles: {
			type: Array,
			required: true,
			default: () => [],
		},
		documentIndex: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			showSentences: this.selectedFiles.length <= 1,
			completely_generated_prob_config: loadState('integration_gptzero', 'completely_generated_prob_config'),
			average_generated_prob_config: loadState('integration_gptzero', 'average_generated_prob_config'),
		}
	},
	computed: {
		average_generated_prob() {
			return this.document.average_generated_prob
		},
		completely_generated_prob() {
			return this.document.completely_generated_prob
		},
		completely_generated_prob_percent() {
			return this.document.completely_generated_prob * 100
		},
		overall_burstiness() {
			return this.document.overall_burstiness
		},
		sentences() {
			return this.document.sentences.filter(s => s.sentence.length > 7)
		},
		documentName() {
			return this.selectedFiles.length > 0 ? this.selectedFiles[this.documentIndex].name : t('integration_gptzero', 'Document overall results')
		},
		documentFilePath() {
			return this.selectedFiles.length > 0 ? this.selectedFiles[this.documentIndex].path : t('integration_gptzero', 'Document overall results')
		},
		pathToFilesApp() {
			return generateUrl('/apps/files/?dir=' + this.documentFilePath.replace(this.documentName, '') + '&scrollto=' + this.documentName)
		},
	},
	methods: {
		completelyGeneratedProbText() {
			if (this.document.completely_generated_prob < this.completely_generated_prob_config.min) {
				if (this.document.average_generated_prob > this.average_generated_prob_config) {
					return t('integration_gptzero', 'May include parts written by an AI')
				}
				return t('integration_gptzero', 'Most likely to be written by a human')
			} else if (this.document.completely_generated_prob > this.completely_generated_prob_config.max) {
				if (this.document.average_generated_prob <= this.average_generated_prob_config) {
					return t('integration_gptzero', 'May include parts written by an AI')
				}
				return t('integration_gptzero', 'Most likely generated by an AI')
			} else {
				if (this.document.average_generated_prob > this.average_generated_prob_config) {
					return t('integration_gptzero', 'May include parts written by an AI')
				}
				return t('integration_gptzero', 'Partially based on input or other sources. Unknown really')
			}
		},
	},
}
</script>

<style lang="scss" scoped>
.scan-results {
	width: 100%;
	padding: 10px;
	margin: 20px auto;
	// border: 1px solid var(--color-border-dark);
	// border-radius: var(--border-radius);

	&__heading {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin-bottom: 10px;

		h3 {
			display: flex;
			align-items: center;
			margin: 0;
			font-weight: normal;

			a {
				margin-left: 10px;
			}
		}

		&_result {
			font-weight: bold;

			&-subline {
				font-weight: normal;
			}
		}
	}

	& .overall-results {
		margin-bottom: 1rem;
	}

	.triangle-icon-rotate {
		transform: rotate(180deg);
	}

	.document-sentences {
		display: flex;
		align-items: center;
		cursor: pointer;
		user-select: none;

		.material-design-icon {
			margin-left: 10px;
		}
	}

	.sentences {
		border: 1px solid var(--color-border-dark);
		border-radius: var(--border-radius);
		padding: 5px 10px;
	}
}
</style>
