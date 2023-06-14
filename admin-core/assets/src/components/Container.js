import React, { useState, useEffect, useRef } from 'react';
import { useLocation, useHistory } from 'react-router-dom';
import { __ } from '@wordpress/i18n';

import Notification from '@Admin/components/tabs/Notification';
import Header from '@Admin/components/Header';
import Settings from '@Admin/components/path/Settings';
import Contents from '@Admin/components/path/Contents';
import apiFetch from '@wordpress/api-fetch';
import { useStateValue } from '@Admin/components/Data';

function Container() {
	const [ data ] = useStateValue();
	const [ settingsTab, setSettingsTab ] = useState( '' );
	const query = new URLSearchParams( useLocation().search );
	const activePage = 'md_ai_content_writer_settings';
	const [ activePath, setActivePath ] =  useState( 'settings' );
	const tab = [
		'md_ai_content_writer_setting',
		'md_ai_content_writer_styling',
		'how',
	].includes( query.get( 'tab' ) )
		? query.get( 'tab' )
		: getSettingsTab();
	const [ processing, setProcessing ] = useState( false );

	const [ status, setStatus ] = useState( false );

	const updateData = useRef( false );

	useEffect( () => {
		setActivePath( query.get( 'path' ) );
	}, [query] );

	useEffect( () => {
		if ( ! updateData.current ) {
			updateData.current = true;
			return;
		}

		const formData = new window.FormData();

		formData.append( 'action', 'md_ai_content_writer_update_settings' );
		formData.append(
			'security',
			md_ai_content_writer_settings.update_nonce
		);
		formData.append(
			'md_ai_content_writer_general',
			JSON.stringify( data.md_ai_content_writer_general )
		);
		formData.append(
			'md_ai_content_writer_general_appearance',
			JSON.stringify( data.md_ai_content_writer_general_appearance )
		);

		setProcessing( true );

		apiFetch( {
			url: md_ai_content_writer_settings.ajax_url,
			method: 'POST',
			body: formData,
		} ).then( () => {
			setProcessing( false );
			setStatus( true );
			setTimeout( () => {
				setStatus( false );
			}, 2000 );
		} );
	}, [ data ] );

	const history = useHistory();
	const navigation = [
		{
			name: __( 'General Settings', 'md-ai-content-writer' ),
			slug: 'md_ai_content_writer_setting',
		},
	];

	const lang_navigation = [
		{
			name: __( 'Header', 'md-ai-content-writer' ),
			slug: 'md_ai_content_writer_setting',
		},
	];

	lang_navigation.push( {
		name: __( 'Footer', 'md-ai-content-writer' ),
		slug: 'md_ai_content_writer_styling',
	} );

	function navigate( navigateTab ) {
		setSettingsTab( navigateTab );
		history.push(
			'admin.php?page=md_ai_content_writer_settings&path=settings&tab=' +
				navigateTab
		);
	}

	function getSettingsTab() {
		return settingsTab || 'md_ai_content_writer_setting';
	}

	return (
		<form
			className="BevoshelvesWcDokanAddonSettings"
			id="BevoshelvesWcDokanAddonSettings"
			method="post"
		>
			<Header
				processing={ processing }
				activePage={ activePage }
				activePath={ activePath }
			/>
			<Notification status={ status } setStatus={ setStatus } />
			{ 'settings' === activePath ? (
				<Settings
					navigation={ navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) : 'contents' === activePath ? (
				<Contents
					navigation={ lang_navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) : (
				<Settings
					navigation={ navigation }
					tab={ tab }
					navigate={ navigate }
				/>
			) }
		</form>
	);
}

export default Container;
