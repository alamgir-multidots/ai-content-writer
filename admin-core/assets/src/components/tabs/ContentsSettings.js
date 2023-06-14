import React from 'react';
import { __ } from '@wordpress/i18n';
import SectionWrapper from '@Admin/components/wrappers/SectionWrapper';
import DropdownField from '@Admin/components/fields/DropdownField';
import { useStateValue } from '@Admin/components/Data';
import ToggleField from '@Admin/components/fields/ToggleField';
import NumberField from '@Admin/components/fields/NumberField';

function ContentsSettings() {
	const [ data ] = useStateValue();

	return (
		<>
			<SectionWrapper
				heading={ __( 'Content', 'md-ai-content-writer' ) }
			>
				<DropdownField
					title={ __( 'Content Structure', 'md-ai-content-writer' ) }
					description={ __(
						'Choose what type of content structure want to generate.',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[content_structure]' }
					value={ data.md_ai_content_writer_general.content_structure }
					optionsArray={ [
						{
							id: 'article',
							name: __( 'Article', 'md-ai-content-writer' ),
						},
						{
							id: 'opinion',
							name: __( 'Opinion', 'md-ai-content-writer' ),
						},
					] }
				/>
				<DropdownField
					title={ __( 'Content Length', 'md-ai-content-writer' ) }
					description={ __(
						'Choose the content length.',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[content_lenght]' }
					value={ data.md_ai_content_writer_general.content_lenght }
					optionsArray={ [
						{
							id: 'long',
							name: __( 'Long', 'md-ai-content-writer' ),
						},
						{
							id: 'medium',
							name: __( 'Medium', 'md-ai-content-writer' ),
						},
						{
							id: 'short',
							name: __( 'Short', 'md-ai-content-writer' ),
						},
					] }
				/>
				<DropdownField
					title={ __( 'Writing Style', 'md-ai-content-writer' ) }
					description={ __(
						'Choose the content length.',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[writing_style]' }
					value={ data.md_ai_content_writer_general.writing_style }
					optionsArray={ [
						{
							id: 'normal',
							name: __( 'Normal', 'md-ai-content-writer' ),
						},
						{
							id: 'business',
							name: __( 'Business', 'md-ai-content-writer' ),
						},
						{
							id: 'technical',
							name: __( 'Technical', 'md-ai-content-writer' ),
						},
						{
							id: 'marketing',
							name: __( 'Marketing', 'md-ai-content-writer' ),
						},
						{
							id: 'creative',
							name: __( 'Creative', 'md-ai-content-writer' ),
						},
						{
							id: 'news',
							name: __( 'News', 'md-ai-content-writer' ),
						},
						{
							id: 'personal',
							name: __( 'Personal', 'md-ai-content-writer' ),
						},
						{
							id: 'informal',
							name: __( 'Informal', 'md-ai-content-writer' ),
						},
					] }
				/>
				<DropdownField
					title={ __( 'Writing Tone', 'md-ai-content-writer' ) }
					description={ __(
						'Choose the content length.',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[writing_tone]' }
					value={ data.md_ai_content_writer_general.writing_tone }
					optionsArray={ [
						{
							id: 'informative',
							name: __( 'Informative', 'md-ai-content-writer' ),
						},
						{
							id: 'professional',
							name: __( 'Professional', 'md-ai-content-writer' ),
						},
						{
							id: 'approachable',
							name: __( 'Approachable', 'md-ai-content-writer' ),
						},
						{
							id: 'casual',
							name: __( 'Casual', 'md-ai-content-writer' ),
						},
						{
							id: 'serious',
							name: __( 'Serious', 'md-ai-content-writer' ),
						},
						{
							id: 'passionate',
							name: __( 'Passionate', 'md-ai-content-writer' ),
						},
						{
							id: 'soothing',
							name: __( 'Soothing', 'md-ai-content-writer' ),
						},
						{
							id: 'funny',
							name: __( 'Funny', 'md-ai-content-writer' ),
						},
					] }
				/>
				<ToggleField
					title={ __( 'Add excerpt', 'md-ai-content-writer' ) }
					description={ __(
						'Add an introduction beginning of the topics.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value:',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[add_excerpt]' }
					value={ data.md_ai_content_writer_general.add_excerpt }
				/>
				<NumberField
					title={ __( 'Excerpt words', 'md-ai-content-writer' ) }
					description={ __(
						'Set how much excerpt words.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 100',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[excerpt_words]' }
					value={ data.md_ai_content_writer_general.excerpt_words }
				/>
				<ToggleField
					title={ __( 'Add conclusion', 'md-ai-content-writer' ) }
					description={ __(
						'Add conclusion end of the topics.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value:',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[add_conclusion]' }
					value={ data.md_ai_content_writer_general.add_conclusion }
				/>
				<ToggleField
					title={ __( 'Generate Featured Image', 'md-ai-content-writer' ) }
					description={ __(
						'Select this to auto-generate the thumbnail image. It will generate from your main prompt.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value:',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[generate_featured_image]' }
					value={ data.md_ai_content_writer_general.generate_featured_image }
				/>
				<DropdownField
					title={ __( 'Image Size', 'md-ai-content-writer' ) }
					description={ __(
						'Choose the size of the image you want to generate.',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[featured_image_size]' }
					value={ data.md_ai_content_writer_general.featured_image_size }
					optionsArray={ [
						{
							id: '256x256',
							name: __( 'Thumbnail (256x256px)', 'md-ai-content-writer' ),
						},
						{
							id: '512x512',
							name: __( 'Medium (512x512px)', 'md-ai-content-writer' ),
						},
						{
							id: '1024x1024',
							name: __( 'Large (1024x1024px)', 'md-ai-content-writer' ),
						},
					] }
				/>
			</SectionWrapper>
		</>
	);
}

export default ContentsSettings;
