import React, {Component} from "react";

import AddCreditCard from './AddCreditCard';
import AddCheckingAcct from './AddNewCheckingAcct';
import OneTimeFunding from './OneTimeFunding';

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {paymentMethod: null, separator: false}
	}

	componentDidMount = () => {
		$('#add-funds').on('click', function(event) {
			event.preventDefault();
			this.setState({paymentMethod: "oneTimeFunding", separator: true});
		}.bind(this));
	}

	handleSelection = (event) => {
		this.setState({paymentMethod: event.target.value});
		if(this.state.paymentMethod != "default") {
			this.setState({separator: true});
		}
		else {
			this.setState({separator: false})
		}
	}

	renderSeparator = () => {
		if(this.state.separator == true) {
			return(
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-12">
							<hr />
						</div>
					</div>
				</div>
			);
		}
	}

	addFunds = (page) => {
		this.setState({
			paymentMethod: page
		})
	}

    render() {
		let selection = this.state.paymentMethod;
		let component = null;

		switch(selection) {
			case "new_debit":
				component = <AddCreditCard/>;
				break;
			case "oneTimeFunding":
				component = <OneTimeFunding/>;
				break;
			case "new_checking":
				component = <AddCheckingAcct/>;
				break;
			default:
				component = "Select Above";
		}

        return (
        	<div className="animated fadeIn">
				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-4">
							<label>Select Funding Method
								<select onChange={this.handleSelection.bind(this)}>
									<option value="default">-- Select One --</option>
									<option value="new_debit">Add new debit or credit card</option>
									<option value="new_checking">Add new checking account</option>
								</select>
							</label>
						</div>

						<div className="cell large-8 text-right">
							<button id="add-funds" className="button">+ Instant Funds</button>
						</div>
					</div>
				</div>

				{this.renderSeparator()}

				<div className="grid-container">
					<div className="grid-x grid-margin-x">
						<div className="cell large-12">
							{component}
						</div>
					</div>
				</div>
			</div>


        );
    }
}

export default Index;