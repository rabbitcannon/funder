import React, {Component} from "react";
import _ from "underscore";

class FundingOptions extends Component {
	constructor(props) {
		super(props);

		this.state = { allProfiles: this.props.allProfiles }
	}

	componentDidMount = () => {
		this.updatePaymentMethods();
		$('#funding-methods option:first').text('Loading...');
		$('#funding-methods').prop('disabled', true);
	}

	updatePaymentMethods = async () => {
		this.props.updatePaymentMethods();
	}

    render() {
		let profiles = this.props.allProfiles;

		let cardMethods = _.map(profiles.card_profiles, (profile) => {
			if(profiles.card_profiles.length > 0) {
				return <option key={profile.id}
							   value="card"  id={profile.id - 1}>
							{profile.payment_method_nickname}: xxxx-xxxx-xxxx-{profile.last_4_digits}
					</option>;
			}
			else {
				return <option disabled>No Credit/Debit cards</option>;
			}
		});

		let eftMethods = _.map(profiles.eft_profiles, (profile) => {
			if(profiles.eft_profiles.length > 0) {
				return <option key={profile.id}
							   value="eft"  id={profile.id - 1}>
							{profile.payment_method_nickname}: xxxx-xxxx-xxxx-{profile.last_4_digits}
					</option>;
			}
			else {
				return <option disabled>No EFT accounts</option>;
			}
		});

        return (
			<label>Select Funding Method <img id="loader" src="../../images/loaders/loader_black_15.gif" />
				<select id="funding-methods" onChange={this.props.handleSelection.bind(this)}>
					<option value="default">-- Select One --</option>
					{cardMethods}
					<option disabled>──────────</option>
					{eftMethods}
					<option disabled>──────────</option>
					<option value="new_debit">Add new debit or credit card</option>
					<option value="new_checking">Add new checking account</option>
				</select>
			</label>
        );
    }
}

export default FundingOptions;