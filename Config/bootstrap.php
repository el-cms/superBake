<?php

// Use command execution through GUI (experimental)
Configure::write('Sb.executeTroughGUI', true);

// Croogo integration
if (class_exists('Croogo')) {
	Configure::write('Sb.Croogo', true);
	// Menu entry for administration
	CroogoNav::add('sidebar', 'extensions.children.sb', array(
			'title' => 'SuperBake',
			'url' => '#',
			'children' => array(
					'sb1' => array(
							'title' => 'Home',
							'url' => array(
									'admin' => true,
									'plugin' => 'sb',
									'controller' => 'sb',
									'action' => 'index',
							),
					),
					'sb2' => array(
							'title' => 'Tests',
							'url' => '#',
							'children' => array(
									'sb2-1' => array(
											'title' => 'Check config file',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'sb',
													'action' => 'check'
											),
									),
									'sb2-2' => array(
											'title' => 'Summary',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'sb',
													'action' => 'tree',
											),
									),
							),
					),
					'sb3' => array(
							'title' => 'Help',
							'url' => '#',
							'children' => array(
									'sb3-1' => array(
											'title' => 'Help home',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'docs',
													'action' => 'display',
													'help.md',
											)
									),
									'sb3-2' => array(
											'title' => 'About',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'docs',
													'action' => 'display',
													'about.md',
											)
									),
									'sb3-3' => array(
											'title' => 'Config file',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'docs',
													'action' => 'display',
													'help_config.md',
											)
									),
									'sb3-4' => array(
											'title' => 'Extend the shell',
											'url' => array(
													'admin' => true,
													'plugin' => 'sb',
													'controller' => 'docs',
													'action' => 'display',
													'help_shell.md',
											)
									),
									'sb3-5' => array(
											'title' => 'Submit an issue',
											'url' => 'https://github.com/el-cms/superBake/issues?state=open',
											'target' => '_blank',
									),
							)
					)
			),
					)
	);
}
