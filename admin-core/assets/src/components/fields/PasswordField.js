import Password from '@Admin/components/fields/Password';
import FieldWrapper from '@Admin/components/wrappers/FieldWrapper';

function PasswordField( props ) {
	const { title, description } = props;

	return (
		<FieldWrapper title={ title } description={ description }>
			<div class="w-[50%]">
				<Password
					name={ props.name }
					val={ props.value }
					max={ props.max }
				/>
			</div>
		</FieldWrapper>
	);
}

export default PasswordField;
