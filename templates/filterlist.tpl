{% for item in items %}
<div class="">
	<p>{% if item.rss_name %}{{ item.rss_name }} \ {% endif %}{{ item.title }}{% if item.file_name != '' %}<a href="t/{{ item.id }}">.torrent</a>{% endif %}</p>
</div>
{% endfor %}