<template>
	<div id="gptzero_prefs" class="section">
		<h2>
			<GPTZeroIcon class="gptzero-icon" />
			{{ t('integration_gptzero', 'GPTZero integration') }}
		</h2>
		<p class="settings-hint">
			{{ t('integration_gptzero', 'Put the "API key" below.') }}
		</p>
		<div class="field">
			<label for="gptzero-api-token">
				<KeyIcon :size="20" class="icon" />
				{{ t('integration_gptzero', 'GPTZero API key') }}
			</label>
			<input id="gptzero-api-token"
				v-model="state.api_token"
				type="password"
				:readonly="readonly"
				:placeholder="t('integration_gptzero', 'GPTZero API access key')"
				@focus="readonly = false"
				@input="onInput">
		</div>
	</div>
</template>

<script>
import KeyIcon from 'vue-material-design-icons/Key.vue'

import axios from '@nextcloud/axios'
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

import GPTZeroIcon from './icons/GPTZeroIcon.vue'

export default {
	name: 'AdminSettings',

	components: {
		GPTZeroIcon,
		KeyIcon,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_gptzero', 'admin-config'),
			// to prevent some browsers to fill fields with remembered passwords
			readonly: true,
		}
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		onInput() {
			delay(() => {
				this.saveOptions({
					api_token: this.state.api_token,
				})
			}, 2000)()
		},
		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_gptzero/admin-config')
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_gptzero', 'GPTZero admin options saved'))
			}).catch((error) => {
				showError(
					t('integration_gptzero', 'Failed to save GPTZero admin options')
					+ ': ' + (error.response?.request?.responseText ?? '')
				)
				console.error(error)
			})
		},
	},
}
</script>

<style scoped lang="scss">
#gptzero_prefs {
	.field {
		display: flex;
		align-items: center;
		margin-left: 30px;

		input,
		label {
			width: 300px;
		}

		label {
			display: flex;
			align-items: center;
		}
		.icon {
			margin-right: 8px;
		}
	}

	.settings-hint {
		display: flex;
		align-items: center;
	}

	h2 {
		display: flex;
		.gptzero-icon {
			margin-right: 12px;
		}
	}
}
</style>
