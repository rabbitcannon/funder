import React, {Component} from "react";

class AddNewCheckingAcct extends Component {


    render() {
		const styles = {
			hidden: {
				display: 'none'
			}
		}

        return (
			<div className="card animated fadeIn">
				<div className="card-divider">
					<h4>Add a new checking account</h4>
				</div>
				<div className="card-section">

					<form id="add-checking-form" data-abide noValidate>
						<div className="grid-container">

							<div className="grid-x grid-margin-x">
								<div className="cell medium-12">
									<div data-abide-error className="alert callout" style={styles.hidden}>
										<p><i className="fi-alert"></i> There are some errors in your form.</p>
									</div>
								</div>
							</div>
						</div>

						Checking Account.
					</form>

				</div>
			</div>
        );
    }
}

export default AddNewCheckingAcct;