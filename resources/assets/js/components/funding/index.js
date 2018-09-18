import React, {Component} from "react";

import Header from "../layout/Header";
import FundingMethods from './FundingMethods';
import LoginForm from "../login/LoginForm";

class Index extends Component {
	constructor(props) {
		super(props);

		this.state = {
			page: "",
			playerData: this.props.playerData || {}
		}
	}

    render() {
        return (
            <div>
				<Header playerData={this.state.playerData} />
				<FundingMethods/>
			</div>
        );
    }
}

export default Index;