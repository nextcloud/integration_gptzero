# integration_gptzero
Nextcloud GPTZero integration to detect AI-generated content

It allows to send text or files to GPTZero API and detect if the content is generated by an AI.

Possible results are:
* Most likely written by a Human
* May include parts written by AI
* Partially based on input or other sources. Unknown really
* Most likely generated by an AI

Formula to detect generated content is:
```	
if X < completely_generated_prob.min:
	if average_generated_prob <= Y:
  		return `Most likely written by a Human`
	return `May include parts written by AI`
else if X > completely_generated_prob.max:
	if average_generated_prob <= Y:
  		return `May include parts written by AI`
	return `Most likely generated by an AI`
else:
	if average_generated_prob <= Y:
		return `Unknown really`
	return `May include parts written by AI`
```

## 🔧 Configuration

### Admin settings

Visit "Connected accounts" **admin** settings section and fill `GPTZero API key`

There is `Completely generated probability` minimum and maximum values and `Average generated probability` value.

## 🛠️ State of maintenance

While there are some things that could be done to further improve this app, the app is currently maintained with **limited effort**. This means:

* The main functionality works for the majority of the use cases
* We will ensure that the app will continue to work like this for future releases and we will fix bugs that we classify as 'critical'
* We will not invest further development resources ourselves in advancing the app with new features
* We do review and enthusiastically welcome community PR's

We would be more than excited if you would like to collaborate with us. We will merge pull requests for new features and fixes. We also would love to welcome co-maintainers.

If you are a customer of Nextcloud and you have a strong business case for any development of this app, we will consider your wishes for our roadmap. Please contact your account manager to talk about the possibilities.
