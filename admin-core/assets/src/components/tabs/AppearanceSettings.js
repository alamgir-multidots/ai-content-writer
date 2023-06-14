import React from 'react';
import { __ } from '@wordpress/i18n';
import SectionWrapper from '@Admin/components/wrappers/SectionWrapper';
import ColorField from '@Admin/components/fields/ColorField';
import { useStateValue } from '@Admin/components/Data';

function FrenchSettings() {
	const [ data ] = useStateValue();

	return (
		<>
			<SectionWrapper
				heading={ __( 'Appearance', 'md-ai-content-writer' ) }
			>
				<ColorField
					title={ __( 'Primary color', 'md-ai-content-writer' ) }
					description={ __(
						'Choose color for primary color.',
						'md-ai-content-writer'
					) }
					name={
						'md_ai_content_writer_general_appearance[primary_bg_color]'
					}
					value={
						data.md_ai_content_writer_general_appearance.primary_bg_color
					}
					default={ '#ECECEE' }
				/>
				<ColorField
					title={ __(
						'Primary text color',
						'md-ai-content-writer'
					) }
					description={ __(
						'Choose color for primary text color.',
						'md-ai-content-writer'
					) }
					name={
						'md_ai_content_writer_general_appearance[primary_font_color]'
					}
					value={
						data.md_ai_content_writer_general_appearance.primary_font_color
					}
					default={ '#000000' }
				/>
			</SectionWrapper>
		</>
	);
}

export default FrenchSettings;
