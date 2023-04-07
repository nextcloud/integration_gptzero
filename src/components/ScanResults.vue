<template>
	<div class="scan-results">
		<div class="overall-results">
			<p>
				{{ t('integration_gptzero', 'Average generated probability:') }}
				{{ average_generated_prob }}
			</p>
			<p>
				{{ t('integration_gptzero', 'Completely generated probability:') }}
				{{ completely_generated_prob }}
			</p>
			<p>
				{{ t('integration_gptzero', 'Overall burstiness:') }}
				{{ overall_burstiness }}
			</p>
		</div>
		<div v-if="false" class="paragraphs">
			<ParagraphMark v-for="paragraph in paragraphs"
				:key="paragraph.id"
				:paragraph="paragraph" />
		</div>
		<div class="sentences">
			<SentenceMark v-for="sentence in sentences"
				:key="sentence.id"
				:sentence="sentence" />
		</div>
	</div>
</template>

<script>
import ParagraphMark from './ParagraphMark.vue'
import SentenceMark from './SentenceMark.vue'

export default {
	name: 'ScanResults',
	components: {
		ParagraphMark,
		SentenceMark,
	},
	props: {
		document: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
		}
	},
	computed: {
		average_generated_prob() {
			return this.document.average_generated_prob.toFixed(2)
		},
		completely_generated_prob() {
			return this.document.completely_generated_prob.toFixed(2)
		},
		overall_burstiness() {
			return this.document.overall_burstiness.toFixed(2)
		},
		paragraphs() {
			return this.document.paragraphs
		},
		sentences() {
			return this.document.sentences
		},
	},
	methods: {
	},
}
</script>

<style lang="scss" scoped>
.scan-results {
	width: 100%;

	& .overall-results {
		margin-bottom: 1rem;
	}
}
</style>
