<template>
	<NcContent app-name="integration_gptzero">
		<NcAppContent>
			<div class="gptzero-app">
				<h2>
					<GPTZeroIcon class="gptzero-icon" />
					{{ t('integration_gptzero', 'GPTZero integration') }}
				</h2>
				<textarea v-model="text"
					cols="80"
					rows="5" />
				<div class="actions">
					<NcButton :disabled="predicting"
						@click="postPredictText">
						<template #icon>
							<span v-if="predicting" class="material-icon icon-loading-small" />
						</template>
						{{ t('integration_gptzero', 'Predict') }}
					</NcButton>
					<NcButton style="margin: 20px 0;"
						@click="fillWithTestText">
						{{ t('integration_gptzero', 'Fill with test text') }}
					</NcButton>
				</div>
				<textarea v-if="predictResult !== {} && predictResult !== null"
					cols="80"
					rows="5"
					:value="predictResultText" />
			</div>
		</NcAppContent>
	</NcContent>
</template>

<script>
import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'

import NcContent from '@nextcloud/vue/dist/Components/NcContent.js'
import NcAppContent from '@nextcloud/vue/dist/Components/NcAppContent.js'
import NcButton from '@nextcloud/vue/dist/Components/NcButton.js'
import GPTZeroIcon from './components/icons/GPTZeroIcon.vue'

export default {
	name: 'App',
	components: {
		NcContent,
		NcAppContent,
		NcButton,
		GPTZeroIcon,
	},
	data() {
		return {
			text: '',
			predictResult: {},
			predicting: false,
		}
	},
	computed: {
		predictResultText() {
			return JSON.stringify(this.predictResult)
		},
	},
	methods: {
		postPredictText() {
			if (this.text !== '' && this.text.length > 250) {
				this.predicting = true
				this.predictResult = {}
				axios.post(generateUrl('/apps/integration_gptzero/predict/text'), {
					text: this.text.trim().replace(/(?:\r\n|\r|\n)/g, '\\n'),
				}).then(res => {
					console.debug(res)
					this.predictResult = res.data
					this.predicting = false
				})
			} else {
				showError(t('integration_gptzero', 'Please enter at least 250 characters.'))
			}
		},
		fillWithTestText() {
			this.text = `Climate change refers to the long-term shift in global weather patterns caused by human activity, particularly the emission of greenhouse gases into the atmosphere.
The most significant greenhouse gas is carbon dioxide, which is primarily produced by burning fossil fuels such as coal, oil, and gas.
The consequences of climate change are already visible in the form of rising temperatures, melting glaciers and ice caps, and more frequent extreme weather events such as hurricanes, droughts, and floods.
Scientists said many of the species relied on snow cover remaining high on hills until late spring and even summer to ensure a moist environment.
They also said plants that thrived on lower ground in warmer conditions were spreading to mountain habitats.
Species found to be in decline include snow pearlwort, alpine lady-fern and alpine speedwell.
These changes have significant impacts on ecosystems, biodiversity, and human health, including increased risk of respiratory diseases, food and water shortages, and the spread of infectious diseases.
To address climate change, it is essential to reduce greenhouse gas emissions through a range of measures, including increased use of renewable energy sources, greater energy efficiency, and improved transportation systems.
Many countries have committed to reducing their carbon footprint through initiatives such as the Paris Agreement, which aims to limit global warming to below 2°C above pre-industrial levels.
`
		},
	},
}
</script>

<style lang="scss" scoped>
.gptzero-app {
	display: flex;
	justify-content: center;
	flex-direction: column;
	align-items: center;
	margin: 0 auto;
	width: 100%;
	height: 100%;

	textarea {
		width: auto;
	}

	.actions {
		margin: 20px 0;
		display: flex;
		align-items: center;

		button:first-child {
			margin-right: 20px;
		}
	}

	h2 {
		display: flex;

		.gptzero-icon {
			margin-right: 10px;
		}
	}
}
</style>
