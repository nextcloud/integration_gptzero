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

function generateGPTZeroAppUrl(filePath) {
	return generateUrl('/apps/integration_gptzero?filePath={path}', { path: filePath })
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
})
