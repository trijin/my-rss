{% extends 'main.tpl' %}

{% block title %}Filter list{% endblock %}
{% block fltractive %} class="active"{% endblock %}
{% block contaner %}

{% block addform %}
<div id="filterAdd">
	<h1>Новый Filter</h1>
	<form class="form-horizontal" role="form" method="POST">
		<div class="form-group">
			<label for="name" class="col-sm-2 control-label">Name:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" name="name" id="name" placeholder="Name this filter" required>
			</div>
			<label for="tag" class="col-sm-2 control-label">Tag:</label>
			<div class="col-sm-4">
				<input type="text" class="form-control" name="tag" id="tag" placeholder="Tag for RSS">
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
{# 		<div class="form-group">
			<label for="dirname" class="col-sm-2 control-label">Dir Name:</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" name="dirname" id="dirname" placeholder="Dirname">
			</div>
		</div> #}
		<div class="form-group">
			<label for="rsslist_id" class="col-sm-2 control-label">RSS:</label>
			<div class="col-sm-10">
				<select class="form-control input-lg" name="rsslist_id" id="rsslist_id">
					{% for rss in rsss %}
						<option value="{{ rss.id }}">{{ rss.name }}</option>
					{% endfor %}
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">ADD</button>
				<button type="button" class="btn" onclick="previewFilter('filterAdd');return false;">Check</button>
			</div>
		</div>
		<div id="filterAddList">
		</div>
	</form>

</div>
{% endblock addform %}
{% if filterss|length > 0 %}
	<h1>Список RSS</h1>

	{% for RSS in filterss %}
	<h2 style="cursor:pointer;" onclick="$('#RSS{{ RSS.id }}').toggle();return false;">{{ RSS.name|e('html') }} <small>({{ RSS.list|length }})</small></h2>
	<div id="RSS{{ RSS.id }}" style="display:none;">
		{% for filters in RSS.list %}
		<div id="filterAdd{{ filters.id }}">
			<form class="form-horizontal" role="form" method="POST">
				<input type="hidden" name="editfilterid" value="{{ filters.id }}"/>
				<h3>{{ filters.rss_name|e('html') }} \ {{ filters.name|e('html') }}
					<small><a href="{{ roorURI }}getrss/{{ filters.id }}">rss</a></small>
					 <a onclick="$('.form4edit{{ filters.id }}').toggle();return false;" class="btn btn-sm">edit</a>
				</h3>
				<div class="form4edit{{ filters.id }}">
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="button" class="btn" onclick="previewFilter('filterAdd{{ filters.id }}');return false;">Check</button>
						</div>
					</div>
				</div>
				<div class="form4edit{{ filters.id }}" style="display:none;">
					<div class="form-group">
						<label for="name{{ filters.id }}" class="col-sm-2 control-label">Name:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="name" id="name{{ filters.id }}" placeholder="Name this filter" required value="{{ filters.name|e('html_attr') }}">
						</div>
						<label for="tag{{ filters.id }}" class="col-sm-2 control-label">Tag:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="tag" id="tag{{ filters.id }}" placeholder="Tag for RSS" value="{{ filters.tag|e('html_attr') }}">
						</div>
					</div>
					<div class="form-group">
						<label for="include{{ filters.id }}" class="col-sm-2 control-label">Include:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="include" id="include{{ filters.id }}" placeholder="Include" required value="{{ filters.include|e('html_attr') }}">
						</div>
					</div>
					<div class="form-group">
						<label for="exclude{{ filters.id }}" class="col-sm-2 control-label">Exclude:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="exclude" id="exclude{{ filters.id }}" placeholder="Exclude" value="{{ filters.exclude|e('html_attr') }}">
						</div>
					</div>
{# 					<div class="form-group">
						<label for="dirname{{ filters.id }}" class="col-sm-2 control-label">Dir Name:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="dirname" id="dirname{{ filters.id }}" placeholder="Dirname" value="{{ filters.dirname|e('html_attr') }}">
						</div>
					</div> #}
					<div class="form-group">
						<label for="rsslist_id{{ filters.id }}" class="col-sm-2 control-label">RSS:</label>
						<div class="col-sm-10">
							<select class="form-control input-lg" name="rsslist_id" id="rsslist_id{{ filters.id }}">
								{% for rss in rsss %}
									<option value="{{ rss.id }}"{% if filters.rsslist_id == rss.id %}selected{% endif %}>{{ rss.name }}</option>
								{% endfor %}
							</select>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">SAVE</button>
			          		<input type="submit" class="btn" name="delete" value="DELETE"/>
							<button type="button" class="btn" onclick="previewFilter('filterAdd{{ filters.id }}');return false;">Check</button>
						</div>
					</div>
				</div>
				<div id="filterAdd{{ filters.id }}List">
				</div>
			</form>
		</div>
		{% endfor %}
	</div>
	{% endfor %}

{% endif %}
{#{# block('addform') }#}
{% if flash.app %}
	 <iframe width="0" height="0" src="{{ roorURI }}cron.php" style="visibility: hidden;"></iframe>
{% endif %}


{% endblock %}