<template>
	<div v-tooltip="perplexityText" class="sentence-mark">
		<p class="sentence-mark__text"
			:class="{'sentence-mark__text_generated': likelyGenerated}">
			{{ sentence.sentence }}
		</p>
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
		perplexityText() {
			return t('integration_gptzero', 'Perplexity: {perplexity}', { perplexity: this.sentence.perplexity })
		},
		likelyGenerated() {
			return this.sentence.generated_prob === 1 || this.sentence.generated_prob >= 0.9 || this.sentence?.highlight_for_ai
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
    display: inline;
    line-height: 1.25rem;

    &_generated {
      margin-bottom: 5px;
      background-color: rgba(var(--color-warning-rgb), 0.2);
    }
  }
}
</style>
