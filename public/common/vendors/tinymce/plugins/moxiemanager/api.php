/**
 * Plugin.js
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/*jshint smarttabs:true, undef:true, unused:true, latedef:true, curly:true, bitwise:true, camelcase:false */
/*global moxman:true */

moxman.require([
	"moxman/PluginManager",
	"moxman/vfs/FileSystemManager",
	"moxman/util/JsonRpc"
], function(PluginManager, FileSystemManager, JsonRpc) {
	PluginManager.add("favorites", function(manager) {
		function addFavorite() {
			JsonRpc.exec('favorites.add', {paths: manager.getSelectedFiles().toPathArray()});
		}

		function removeFavorite() {
			var paths = [];

			manager.getSelectedFiles().each(function(file) {
				paths.push(file.info.link);
			});

			JsonRpc.exec('favorites.remove', {paths: paths}, function() {
				manager.refresh();
			});
		}

		function gotoFile() {
			FileSystemManager.getFile(manager.getSelectedFiles()[0].info.link, function(file) {
				manager.open(file);
			});
		}

		manager.on('BeforeRenderManageMenu', function(e) {
			var menu = e.menu;

			if (manager.currentDir.path == '/Favorites') {
				e.preventDefault();

				menu.append({text: 'Remove favorite', onclick: removeFavorite});
				menu.append({text: 'Goto file', onclick: gotoFile});
			}
		});

		manager.addMenuItem({
			text: 'Add favorite',
			icon: 'favorites',
			onclick: addFavorite,
			contexts: ['manage.tools']
		});
	});
});
/**
 * Plugin.js
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/*jshint smarttabs:true, undef:true, unused:true, latedef:true, curly:true, bitwise:true, camelcase:false */
/*global moxman:true */

moxman.require([
	"moxman/PluginManager",
	"moxman/vfs/FileSystemManager",
	"moxman/util/JsonRpc"
], function(PluginManager, FileSystemManager, JsonRpc) {
	PluginManager.add("history", function(manager) {
		function removeHistory() {
			var paths = [];

			manager.getSelectedFiles().each(function(file) {
				paths.push(file.info.link);
			});

			JsonRpc.exec('history.remove', {paths: paths}, function() {
				manager.refresh();
			});
		}

		function gotoFile() {
			FileSystemManager.getFile(manager.getSelectedFiles()[0].info.link, function(file) {
				manager.open(file);
			});
		}

		manager.on('BeforeRenderManageMenu', function(e) {
			var menu = e.menu;

			if (manager.currentDir.path == '/History') {
				e.preventDefault();

				menu.append({text: 'Remove link', onclick: removeHistory});
				menu.append({text: 'Goto file', onclick: gotoFile});
			}
		});
	});
});
/**
 * Plugin.js
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/*jshint smarttabs:true, undef:true, unused:true, latedef:true, curly:true, bitwise:true, camelcase:false */
/*global moxman:true */

moxman.require([
	"moxman/PluginManager",
	"moxman/vfs/FileSystemManager",
	"moxman/util/JsonRpc"
], function(PluginManager, FileSystemManager, JsonRpc) {
	PluginManager.add("uploaded", function(manager) {
		function removeUploaded() {
			var paths = [];

			manager.getSelectedFiles().each(function(file) {
				paths.push(file.info.link);
			});

			JsonRpc.exec('uploaded.remove', {paths: paths}, function() {
				manager.refresh();
			});
		}

		function gotoFile() {
			FileSystemManager.getFile(manager.getSelectedFiles()[0].info.link, function(file) {
				manager.open(file);
			});
		}

		manager.on('BeforeRenderManageMenu', function(e) {
			var menu = e.menu;

			if (manager.currentDir.path == '/Uploaded') {
				e.preventDefault();

				menu.append({text: 'Remove link', onclick: removeUploaded});
				menu.append({text: 'Goto file', onclick: gotoFile});
			}
		});
	});
});
