<template>
	<span :aria-hidden="!title"
		:aria-label="title"
		class="material-design-icon gptzero-icon"
		role="img"
		v-bind="$attrs"
		@click="$emit('click', $event)">
		<svg :fill="fillColor"
			:width="size"
			:height="size"
			enable-background="new 0 0 15 15"
			version="1.0"
			viewBox="0 0 521 479"
			xmlns="http://www.w3.org/2000/svg"
			xmlns:xlink="http://www.w3.org/1999/xlink">
			<g>
				<g transform="translate(0 479) scale(.1 -.1)">
					<path d="m2375 4364c-467-66-866-259-1185-574-297-293-484-633-571-1040-20-93-23-135-23-377-1-149 1-273 4-276s421-7 930-9l925-3 3 273c2 254 1 272-15 272h-635c-555 0-618 1-632 16-9 9-14 24-12 33 3 9 8 30 11 46 21 94 93 264 161 378 176 293 483 542 788 641 196 64 244 71 486 71 183-1 238-4 295-19 218-56 370-122 528-230 54-36 101-66 105-66s92 85 197 190l189 189-19 21c-44 49-277 197-425 270-90 45-340 135-443 159-200 47-474 61-662 35z" :fill="fillColor" fill-opacity="1" />
				</g>
				<g transform="matrix(-.1 0 0 .1 521.49 7.5567)" fill-opacity=".14493">
					<path d="m2375 4364c-467-66-866-259-1185-574-297-293-484-633-571-1040-20-93-23-135-23-377-1-149 1-273 4-276s421-7 930-9l925-3 3 273c2 254 1 272-15 272h-635c-555 0-618 1-632 16-9 9-14 24-12 33 3 9 8 30 11 46 21 94 93 264 161 378 176 293 483 542 788 641 196 64 244 71 486 71 183-1 238-4 295-19 218-56 370-122 528-230 54-36 101-66 105-66s92 85 197 190l189 189-19 21c-44 49-277 197-425 270-90 45-340 135-443 159-200 47-474 61-662 35z" :fill="fillColor" fill-opacity="1" />
				</g>
			</g>
		</svg>
	</span>
</template>

<script>
export default {
	name: 'GPTZeroIcon',
	props: {
		title: {
			type: String,
			default: '',
		},
		fillColor: {
			type: String,
			default: 'currentColor',
		},
		size: {
			type: Number,
			default: 24,
		},
	},
}
</script>
