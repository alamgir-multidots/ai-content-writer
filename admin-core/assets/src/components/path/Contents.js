import ContentsSettings from '@Admin/components/tabs/ContentsSettings';

function Contents( props ) {
	const { navigation, tab, navigate } = props;
	return (
		<main className="pl-2 pt-5">
			<div className="max-w-[98%] bg-white shadow rounded">
				<div className="mb-0 sm:px-6 lg:px-0 lg:col-span-9">
					{ 'md_ai_content_writer_setting' === tab && (
						<>
							<ContentsSettings />
						</>
					) }
				</div>
			</div>
		</main>
	);
}

export default Contents;
