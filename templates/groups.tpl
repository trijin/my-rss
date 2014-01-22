{% extends 'main.tpl' %}

{% block title %}Groups list{% endblock %}
{% block groupactive %} class="active"{% endblock %}
{% block contaner %}

{% block addform %}
<div id="groupAdd">
	<h1>Новый group</h1>
	<form class="form-horizontal" role="form" method="POST">
		<div class="form-group">
			<label for="name" class="col-sm-2 control-label">Name:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" name="name" id="name" placeholder="Name this group" required>
			</div>
			<label for="tag" class="col-sm-2 control-label">Tag:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" name="tag" id="tag" placeholder="Tag for RSS">
			</div>
		</div>
		<div class="form-group">
			<label for="ru_name" class="col-sm-2 control-label">Ru-Name:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" name="ru_name" id="ru_name" placeholder="Name this group" required>
			</div>
		</div>
		<div class="form-group">
			<label for="include" class="col-sm-2 control-label">Include:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="include" id="include" placeholder="Include" required>
			</div>
		</div>
		<div class="form-group">
			<label for="exclude" class="col-sm-2 control-label">Exclude:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="exclude" id="exclude" placeholder="Exclude">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">ADD</button>
				<button type="button" class="btn" onclick="previewFilter('groupAdd');return false;">Check</button>
			</div>
		</div>
		<div id="groupAddList">
		</div>
	</form>

</div>
{% endblock addform %}

{% if items|length > 0 %}
	<h1>не в группе</h1>
	{% for item in items %}
		<div class="thumbnail">
				<h3>{{ item.rss_name|e('html') }}{% if item.filter_name %} \ {{ item.filter_name|e('html') }} {% endif %}
				<small>{{ item.title|e('html') }}</small></h3><small>{{ item.item_time|date('Y-m-d H:i') }}</small>
				{# <div>{{ item.description|raw }}</div> #}
		</div>
	{% endfor %}

{% endif %}

{% if groups|length > 0 %}
	<h1>Список Групп</h1>
	{% for group in groups %}
	<div id="groupAdd{{ group.id }}">
		<form class="form-horizontal" role="form" method="POST">
			<input type="hidden" name="editgroupid" value="{{ group.id }}"/>
			<h3>{{ group.name|raw }} / {{ group.ru_name|raw }}
				 <a onclick="$('.form4edit{{ group.id }}').toggle();return false;" class="btn btn-sm">edit</a>
			</h3>
			<div class="form4edit{{ group.id }}">
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="button" class="btn" onclick="previewFilter('groupAdd{{ group.id }}');return false;">Check</button>
					</div>
				</div>
			</div>
			<div class="form4edit{{ group.id }}" style="display:none;">
				<div class="form-group">
					<label for="name" class="col-sm-2 control-label">Name:</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="name" id="name" placeholder="Name this filter" required value="{{ group.name|e('html_attr') }}">
					</div>
					<label for="tag" class="col-sm-2 control-label">Tag:</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="tag" id="tag" placeholder="Tag for RSS" value="{{ group.tag|e('html_attr') }}">
					</div>
				</div>
				<div class="form-group">
					<label for="ru_name" class="col-sm-2 control-label">Ru-Name:</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="ru_name" id="ru_name" placeholder="Name this filter" required value="{{ group.ru_name|e('html_attr') }}">
					</div>
				</div>
				<div class="form-group">
					<label for="include" class="col-sm-2 control-label">Include:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="include" id="include" placeholder="Include" required value="{{ group.include|e('html_attr') }}">
					</div>
				</div>
				<div class="form-group">
					<label for="exclude" class="col-sm-2 control-label">Exclude:</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="exclude" id="exclude" placeholder="Exclude" value="{{ group.exclude|e('html_attr') }}">
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-offset-2 col-sm-10">
						<button type="submit" class="btn btn-default">SAVE</button>
		          		<input type="submit" class="btn" name="delete" value="DELETE"/>
						<button type="button" class="btn" onclick="previewFilter('groupAdd{{ group.id }}');return false;">Check</button>
					</div>
				</div>
			</div>
			<div id="groupAdd{{ group.id }}List">
			</div>
		</form>
	</div>
	{% endfor %}

{% endif %}
{#{# block('addform') }#}





{% endblock %}