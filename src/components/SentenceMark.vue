<template>
	<div class="sentence-mark">
		<div class="sentence-mark__text"
			:class="{'sentence-mark__text_generated': sentence.generated_prob === 1}"
			:v-tooltip="{content: generatedProbabilityText, placement: 'top'}">
			{{ sentence.sentence }}
		</div>
		<div class="sentence-mark__generated_prob"
			:class="{'sentence-mark__generated_prob_generated': sentence.generated_prob === 1}">
			{{ generatedProbabilityText }}
			{{ perplexityText }}
		</div>
	</div>
</template>

<script>
export default {
	name: 'SentenceMark',
	props: {
		sentence: {
			type: Object,
			required: true,
		},
	},
	data() {
		return {
		}
	},
	computed: {
		generatedProbabilityText() {
			if (this.sentence.generated_prob === 1) {
				return t('integration_gptzero', 'Likely by AI model.')
			}
			return t('integration_gptzero', 'Most likely by a Human.')
		},
		perplexityText() {
			return t('integration_gptzero', 'Perplexity: {perplexity}', { perplexity: this.sentence.perplexity })
		},
	},
	methods: {
	},
}
</script>

<style lang="scss" scoped>
.sentence-mark {
	margin-bottom: 10px;

	&__text {
		font-size: 1.1em;

		&_generated {
			margin-bottom: 5px;
		}
	}

	&__generated_prob {
		font-size: 0.85em;
		color: #5b5600;

		&_generated {
			display: inline;
			padding: 5px 10px;
			background-color: #dcdc00;
			border-radius: var(--border-radius);
		}

		body[data-theme-dark] &_generated {
			color: #000;
		}
	}
}
</style>
