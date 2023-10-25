/**
 * @copyright Copyright (c) 2023 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @copyright Copyright (c) 2023 Alexander Piskun <bigcat88@icloud.com>
 *
 * @author 2023 Andrey Borysenko <andrey18106x@gmail.com>
 *
 * @license AGPL-3.0-or-later
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

import { generateUrl } from '@nextcloud/router'
import { registerFileAction, FileAction } from '@nextcloud/files'

function generateGPTZeroAppUrl(filePath) {
	return generateUrl('/apps/integration_gptzero?filePath={path}', { path: filePath })
}

function getGPTZeroSvgInlineIcon() {
	return '<?xml version="1.0" encoding="UTF-8"?><svg width="521pt" height="479pt" version="1.0" viewBox="0 0 521 479" style="filter: var(--background-invert-if-dark);" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g transform="translate(0 479) scale(.1 -.1)"><path d="m2375 4364c-467-66-866-259-1185-574-297-293-484-633-571-1040-20-93-23-135-23-377-1-149 1-273 4-276s421-7 930-9l925-3 3 273c2 254 1 272-15 272h-635c-555 0-618 1-632 16-9 9-14 24-12 33 3 9 8 30 11 46 21 94 93 264 161 378 176 293 483 542 788 641 196 64 244 71 486 71 183-1 238-4 295-19 218-56 370-122 528-230 54-36 101-66 105-66s92 85 197 190l189 189-19 21c-44 49-277 197-425 270-90 45-340 135-443 159-200 47-474 61-662 35z" fill="#000" fill-opacity="1"/></g><g transform="matrix(-.1 0 0 .1 521.49 7.5567)" fill-opacity=".14493"><path d="m2375 4364c-467-66-866-259-1185-574-297-293-484-633-571-1040-20-93-23-135-23-377-1-149 1-273 4-276s421-7 930-9l925-3 3 273c2 254 1 272-15 272h-635c-555 0-618 1-632 16-9 9-14 24-12 33 3 9 8 30 11 46 21 94 93 264 161 378 176 293 483 542 788 641 196 64 244 71 486 71 183-1 238-4 295-19 218-56 370-122 528-230 54-36 101-66 105-66s92 85 197 190l189 189-19 21c-44 49-277 197-425 270-90 45-340 135-443 159-200 47-474 61-662 35z" fill="#000" fill-opacity="1"/></g></g></svg>'
}

function navigateToAppPage(name, context) {
	// Navigate to the app page by generated url
	let filePath = name
	if (context.dir !== '/') {
		filePath = context.dir + '/' + name
	} else {
		filePath = context.dir + name
	}
	window.location.href = generateGPTZeroAppUrl(filePath)
}

const supportedMimeTypes = {
	pdf: 'application/pdf',
	txt: 'text/plain',
	msword: 'application/msword',
}

Object.keys(supportedMimeTypes).forEach(name => {
	if (OCA.Files && OCA.Files.fileActions) {
		OCA.Files.fileActions.registerAction({
			name: 'gptzeroScan-' + name,
			displayName: t('integration_gptzero', 'Scan with GPTZero'),
			mime: supportedMimeTypes[name],
			order: 90,
			permissions: OC.PERMISSION_READ,
			// files_rightclick only works if iconClass is set
			iconClass: 'icon-gptzero-file',
			actionHandler: (name, context) => {
				navigateToAppPage(name, context)
			},
		})
	} else {
		const action = new FileAction({
			id: 'gptzeroScan-' + name,
			displayName: () => t('integration_gptzero', 'Scan with GPTZero'),
			iconSvgInline: () => getGPTZeroSvgInlineIcon(),
			order: 90,
			enabled(nodes) {
				if (nodes.length !== 1) {
					return false
				}

				return (nodes[0].mime.indexOf(supportedMimeTypes[name]) !== -1)
			},
			async exec(node) {
				navigateToAppPage(node.basename, {
					dir: node.dirname,
				})
				return null
			},
		})
		registerFileAction(action)
	}
})
