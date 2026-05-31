class EmailsController < ApplicationController
  def new
  end

  def index
    @emails = [ Email.first ]
  end

  def create
  end

  def show
    token = Token.find_by(value: params[:token])

    if token.nil? || token.expires_at.past?
      return render json: { error: "Invalid or expired token" }, status: :unauthorized
    end

    render json: token.account.as_json
  end

  private

  def session_params
    params.fetch(:session, {}).permit(:email, :password, :redirect_to)
  end
end
