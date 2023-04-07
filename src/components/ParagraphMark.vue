<template>
	<div class="paragraph-mark">
		<div class="paragraph-mark__start-sentence-index">
			{{ t('integration_gptzero', 'Start sentence index:') }}
			{{ start_sentence_index }}
		</div>
		<div class="paragraph-mark__num-sentences">
			{{ t('integration_gptzero', 'Number of sentences:') }}
			{{ num_sentences }}
		</div>
		<div class="paragraph-mark__completely-generated-prob">
			{{ t('integration_gptzero', 'Completely generated probability:') }}
			{{ completely_generated_prob }}
		</div>
	</div>
</template>

<script>
export default {
	name: 'ParagraphMark',
	props: {
		paragraph: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
		}
	},
	computed: {
		start_sentence_index() {
			return this.paragraph.start_sentence_index
		},
		num_sentences() {
			return this.paragraph.num_sentences
		},
		completely_generated_prob() {
			return this.paragraph.completely_generated_prob.toFixed(2)
		},
	},
	methods: {
	},
}
</script>
