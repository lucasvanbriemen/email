class EmailsController < ApplicationController
  def new
  end

  def index
    @emails = Email.in_group(params[:path]).order(created_at: :desc).page(params[:page])
  end

  def create
  end

  def show
    @email = Email.find(params[:id])

    respond_to do |format|
      format.any { render :show }
    end
  end

  private

  def session_params
    params.fetch(:session, {}).permit(:email, :password, :redirect_to)
  end
end
