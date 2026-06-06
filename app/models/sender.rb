class Sender < ApplicationRecord
  has_many :emails

  def image_url
    "https://img.logo.dev/#{top_level_domain}?token=pk_YHpEPFuOTnGDZ6nmBhgIog&retina=true"
  end

  def top_level_domain
    domain = email.split("@").last
    domain_parts = domain.split(".")
    domain_parts.last(2).join(".")
  end

  def name
    # If the DB name is 0 or blank, fall back to the email prefix.
    db_name = super
    if db_name.present? && db_name != "0"
      db_name
    else
      email.split("@").first
    end
  end
end
