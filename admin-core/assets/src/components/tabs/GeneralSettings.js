import React from 'react';
import { __ } from '@wordpress/i18n';
import SectionWrapper from '@Admin/components/wrappers/SectionWrapper';
import PasswordField from '@Admin/components/fields/PasswordField';
import { useStateValue } from '@Admin/components/Data';
import NumberField from '@Admin/components/fields/NumberField';

function GeneralSettings() {
	const [ data ] = useStateValue();

	return (
		<>
			<SectionWrapper
				heading={ __( 'General', 'md-ai-content-writer' ) }
			>
				<PasswordField
					title={ __( 'API Key', 'md-ai-content-writer' ) }
					description={ __(
						'Enter your API key to use the GPT-3 API.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value:',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[api_key]' }
					value={ data.md_ai_content_writer_general.api_key }
				/>
				<NumberField
					title={ __( 'Temperature', 'md-ai-content-writer' ) }
					description={ __(
						'Control randomness: Lowering results in less random completions. As the temperature approaches zero, the model will become deterministic and repetitive. If it approaches one, the model will become more randomness and creative.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 0.8',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[temperature]' }
					value={ data.md_ai_content_writer_general.temperature }
				/>
				<NumberField
					title={ __( 'Max Tokens', 'md-ai-content-writer' ) }
					description={ __(
						'Set the maximum number of tokens to generate in a single request.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 150',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[max_tokens]' }
					value={ data.md_ai_content_writer_general.max_tokens }
				/>
				<NumberField
					title={ __( 'Top Prediction', 'md-ai-content-writer' ) }
					description={ __(
						'Adjust the Top-P (Top Prediction) parameter to control the diversity of the generated text.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 0.5',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[top_prediction]' }
					value={ data.md_ai_content_writer_general.top_prediction }
				/>
				<NumberField
					title={ __( 'Frequency Penalty', 'md-ai-content-writer' ) }
					description={ __(
						'Adjust the frequency penalty to control the frequency of words in the generated text.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 0',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[frequency_penalty]' }
					value={ data.md_ai_content_writer_general.frequency_penalty }
				/>
				<NumberField
					title={ __( 'Presence Penalty', 'md-ai-content-writer' ) }
					description={ __(
						'Adjust the presence penalty to control the presence of words in the generated text.',
						'md-ai-content-writer'
					) }
					badge={ __(
						'Default value: 0.6',
						'md-ai-content-writer'
					) }
					name={ 'md_ai_content_writer_general[presence_penalty]' }
					value={ data.md_ai_content_writer_general.presence_penalty }
				/>
			</SectionWrapper>
		</>
	);
}

export default GeneralSettings;
