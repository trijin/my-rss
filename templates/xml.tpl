<?xml version="1.0" encoding="UTF-8"?>
<rss version="1.00">
	<channel>
	<title>MyRSS{% if ListName %} \ {{ ListName }}] {% endif %}</title>
	<description>Обновления MyRSS</description>
	<link>{{ mainUrl }}</link>
	<lastBuildDate>{{ maxDate }}</lastBuildDate>
	<language>ru</language>
	<ttl>15</ttl>{% if items|length > 0 %}{% for item in items %}
	
	<item>
		<title><![CDATA[{% if item.tag %}[{{ item.tag }}] {% endif %}{{ item.title }}]]></title>
		<description><![CDATA[{{ item.description }}]]></description>
		<category>{{ item.cat_name }}</category>
		<pubDate>{{ item.date }}</pubDate>
		<link>{{ mainUrl }}t/{{ item.id }}</link>
	</item>{% endfor %}{% endif %}

</rss>