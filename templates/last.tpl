{% extends 'main.tpl' %}

{% block title %}Last torrents{% endblock %}
{% block lastactive %} class="active"{% endblock %}
{% block inheader %}
	<meta http-equiv="refresh" content="1800">
{% endblock %}
{% block contaner %}
<br/>
<div class="alert alert-warning"><a href="{{ roorURI }}last_ung">Last UnGrouped</a></div>
{% if items|length > 0 %}
	<h1>Последние обновления</h1>
	<div class="row" style="display:table-row;">
		{% for item in items %}
			{% if loop.index0 % 3 == 0 %}
				</div>
				<div class="row" style="display:table-row;">
			{% endif %}
			<div class="thumbnail col-sm-3" >
				{% if item.group %}
					<h4>{{ item.group.ru_name }} / {{ item.group.name }}</h4>
				{% else %}
					<h4>{{ item.title|e('html') }}</h4>
				{% endif %}

				{% for time in item.times %}
					
					<h6><b>{{ time.time_ago }}</b> {{ time.rss_name|e('html') }}{% if item.filter_name %} \ {{ time.filter_name|e('html') }} {% endif %}
					<small>{{ item.title|e('html') }}</small></h6>
				{% endfor %}
					{# <div>{{ item.description|raw }}</div> #}
			</div>
			<div class="col-sm-1"></div>
		{% endfor %}
	</div>
	<ul class="pagination pagination-lg">
		{% for page in pages %}
	  	<li{% if page.current == 1 %} class="active"{% endif %}><a href="{{ roorURI }}last{{ page.url }}">{{ page.num }}{% if page.current == 1 %}<span class="sr-only">(current)</span>{% endif %}</a></li>
	  	{% endfor %}
	</ul>

{% endif %}

{% endblock %}