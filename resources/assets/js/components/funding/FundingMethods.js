import React, {Component} from "react";

class FundingMethods extends Component {
    render() {
        return (
			<div className="grid-container">
				<div className="grid-x grid-margin-x">
					<div className="cell large-6">
						<label>Select Funding Method
							<select>
								<option value="new_debit">Add new debit card</option>
								<option value="new_checking">Add new checking account</option>
							</select>
						</label>
					</div>
				</div>
			</div>
        );
    }
}

export default FundingMethods;