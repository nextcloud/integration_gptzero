<template>
	<div class="files-selection">
		<div class="files-selection__files">
			<ul>
				<NcListItem v-for="file in files"
					:key="file.fileid"
					:title="file.path"
					:href="pathToFilesApp(file.name, file.path)"
					:compact="true"
					:aria-label="t('integration_gptzero', 'Selected file to scan for AI')">
					<template #actions>
						<NcActionButton
							v-tooltip="t('integration_gptzero', 'Remove file from selection')"
							:aria-label="t('integration_gptzero', 'Remove file from selection')"
							@click.prevent="removeFileSelection(file)">
							<template #icon>
								<span class="material-icon icon-delete" />
							</template>
						</NcActionButton>
					</template>
					<template #subtitle>
						{{ file.owner }}
						-
						{{ formattedFileSize(file) }}
					</template>
				</NcListItem>
			</ul>
		</div>
	</div>
</template>

<script>
import { generateUrl } from '@nextcloud/router'

import NcListItem from '@nextcloud/vue/dist/Components/NcListItem.js'
import NcActionButton from '@nextcloud/vue/dist/Components/NcActionButton.js'

import { formatBytes } from '../utils/files.js'

export default {
	name: 'FilesSelection',
	components: {
		NcListItem,
		NcActionButton,
	},
	props: {
		files: {
			type: Array,
			required: true,
		},
	},
	data() {
		return {
		}
	},
	computed: {
	},
	methods: {
		removeFileSelection(file) {
			this.$emit('remove-file-selection', file)
		},
		formattedFileSize(file) {
			return formatBytes(file.size)
		},
		pathToFilesApp(name, path) {
			return generateUrl('/apps/files/?dir=' + path.replace(name, '') + '&scrollto=' + name)
		},
	},
}
</script>

<style lang="scss" scoped>
.files-selection {
	width: 80%;
	max-width: 700px;
	margin: 3px auto;

	&__files {
		border: 1px solid var(--color-border-dark);
		border-radius: var(--border-radius);
		padding: 10px;
		height: 130px;
		overflow-y: auto;
	}
}
</style>
