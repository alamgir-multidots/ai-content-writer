export const initialState = md_ai_content_writer_settings;

const reducer = ( state, action ) => {
	switch ( action.type ) {
		case 'CHANGE':
			return {
				...action.data,
			};

		default:
			return state;
	}
};

export default reducer;
