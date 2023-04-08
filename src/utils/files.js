import { generateRemoteUrl } from '@nextcloud/router'
import { getCurrentUser } from '@nextcloud/auth'
import axios from '@nextcloud/axios'

const davRequest = `<?xml version="1.0"?>
	<d:propfind xmlns:d="DAV:" xmlns:oc="http://owncloud.org/ns"
				xmlns:nc="http://nextcloud.org/ns"
				xmlns:ocs="http://open-collaboration-services.org/ns">
		<d:prop>
			<oc:fileid />
			<d:href />
			<d:displayname />
			<d:getcontenttype />
			<d:resourcetype />
			<oc:permissions />
			<oc:size />
			<d:getcontentlength />
			<oc:owner-id />
			<oc:owner-display-name />
		</d:prop>
	</d:propfind>`

const parseFileInfoToObject = (xml) => {
	if (window.DOMParser) {
		const parser = new DOMParser()
		const xmlDoc = parser.parseFromString(xml, 'text/xml')
		const fileid = xmlDoc.getElementsByTagName('oc:fileid')[0].innerHTML
		const href = xmlDoc.getElementsByTagName('d:href')[0].innerHTML
		const name = xmlDoc.getElementsByTagName('d:displayname')[0].innerHTML
		const contentType = xmlDoc.getElementsByTagName('d:getcontenttype')[0].innerHTML
		const size = xmlDoc.getElementsByTagName('oc:size')[0].innerHTML
		const owner = xmlDoc.getElementsByTagName('oc:owner-display-name')[0].innerHTML
		const ownerid = xmlDoc.getElementsByTagName('oc:owner-id')[0].innerHTML
		const permissions = xmlDoc.getElementsByTagName('oc:permissions')[0].innerHTML
		const resourceType = xmlDoc.getElementsByTagName('d:resourcetype')[0].innerHTML
		return {
			fileid,
			href,
			name,
			contentType,
			size,
			owner,
			ownerid,
			permissions,
			resourceType,
		}
	} else {
		return null
	}
}

const requestFileInfo = async (path) => {
	const davPath = `${generateRemoteUrl('dav')}/files/${getCurrentUser().uid}${path}`
	return await axios({
		method: 'PROPFIND',
		url: davPath,
		data: davRequest,
		headers: { details: true, depth: 0 },
	})
}

const formatBytes = (bytes, decimals = 2) => {
	if (bytes === 0) return '0 B'
	const k = 1024
	const dm = decimals < 0 ? 0 : decimals
	const sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
	const i = Math.floor(Math.log(bytes) / Math.log(k))
	return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
}

export {
	requestFileInfo,
	formatBytes,
	parseFileInfoToObject,
}
